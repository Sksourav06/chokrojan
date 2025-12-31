<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketIssue;
use App\Models\SeatLock;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Mail\TicketConfirmationMail;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // ১. ভ্যালিডেশন
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seats' => 'required|array',
            'passenger_mobile' => 'required',
            'passenger_name' => 'required',
            'journey_date' => 'required',
        ]);

        // ডাটাবেজ থেকে অটোমেটিক স্টেশন আইডি বের করা (যদি ফ্রন্টএন্ড থেকে ভুল নামে আসে)
        $schedule = Schedule::find($request->schedule_id);

        // ফ্রন্টএন্ড থেকে আইডি না আসলে শিডিউল টেবিল থেকে আইডি নিয়ে নেওয়া হবে
        $fromStationId = $request->from_station_id ?? $schedule->from_station_id;
        $toStationId = $request->to_station_id ?? $schedule->to_station_id;

        DB::beginTransaction();
        try {
            $seatNumbers = collect($request->seats)->pluck('seat_number')->implode(',');
            $seatCount = count($request->seats);
            $subTotal = collect($request->seats)->sum('fare');

            $discount = $request->discount_amount ?? 0;
            $serviceCharge = $request->service_charge ?? 20;
            $goodsCharge = $request->goods_charge ?? 0;
            $grandTotal = $request->grand_total ?? ($subTotal + $serviceCharge + $goodsCharge - $discount);

            // টিকিট তৈরি
            $ticket = TicketIssue::create([
                'schedule_id' => $request->schedule_id,
                'invoice_no' => 'INV-' . time() . rand(10, 99),
                'pnr_no' => strtoupper(substr(uniqid(), 7)) . rand(10, 99),
                'journey_date' => $request->journey_date,
                'customer_name' => $request->passenger_name,
                'customer_mobile' => $request->passenger_mobile,
                'passenger_email' => $request->passenger_email ?? null,
                'gender' => strtolower($request->passenger_gender ?? 'male'),

                // ফিক্সড আইডি গুলো এখানে
                'from_station_id' => $fromStationId,
                'to_station_id' => $toStationId,
                // boarding_point বা boarding_counter_id যাই আসুক যেন null না হয়
                'boarding_counter_id' => $request->boarding_point ?? $request->boarding_counter_id ?? $fromStationId,
                'dropping_counter_id' => $request->dropping_point ?? $request->dropping_counter_id ?? $toStationId,

                'seat_numbers' => $seatNumbers,
                'seats_count' => $seatCount,
                'sub_total' => $subTotal,
                'discount_amount' => $discount,
                'service_charge' => $serviceCharge,
                'goods_charge' => $goodsCharge,
                'grand_total' => $grandTotal,
                'status_label' => (strtolower($request->ticket_action ?? 'booked') == 'sold') ? 'Sold' : 'Booked',
                'payment_method' => $request->payment_method ?? 'cash',
                'issue_counter_id' => $request->issue_counter_id ?? 'Online',
                'issued_by' => $request->issued_by ?? 'web',
            ]);

            // ২. সিট লক রিলিজ করা
            SeatLock::where('schedule_id', $request->schedule_id)
                ->whereIn('seat_number', collect($request->seats)->pluck('seat_number'))
                ->delete();

            DB::commit();

            // ৩. ইমেইল পাঠানোর আগে ডাটা রিফ্রেশ করা (খুবই গুরুত্বপূর্ণ)
            if ($ticket->passenger_email) {
                try {
                    // refresh() মেথডটি ডাটাবেজ থেকে নতুন সেভ হওয়া আইডিগুলোর নাম টেনে আনবে
                    $ticket->refresh()->load(['fromStation', 'toStation', 'schedule', 'boardingCounter']);
                    Mail::to($ticket->passenger_email)->send(new TicketConfirmationMail($ticket));
                } catch (\Exception $e) {
                    \Log::error("Mail error: " . $e->getMessage());
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Ticket confirmed successfully',
                'ticket_id' => $ticket->id,
                'pnr_no' => $ticket->pnr_no,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function engageSeat(Request $request)
    // {
    //     $existing = SeatLock::where('schedule_id', $request->schedule_id)
    //         ->where('seat_number', $request->seat_number)
    //         ->where('expires_at', '>', now())
    //         ->first();

    //     if ($existing) {
    //         return response()->json(['success' => false, 'message' => 'Seat is already locked.']);
    //     }

    //     SeatLock::create([
    //         'schedule_id' => $request->schedule_id,
    //         'seat_number' => $request->seat_number,
    //         'counter_id' => auth()->user()->counter_id ?? 1, // ডিফল্ট কাউন্টার ১
    //         'expires_at' => now()->addMinutes(2),
    //     ]);

    //     return response()->json(['success' => true]);
    // }


    // public function unlockSeat(Request $request)
    // {
    //     SeatLock::where('schedule_id', $request->schedule_id)
    //         ->where('seat_number', $request->seat_number)
    //         ->delete();

    //     return response()->json(['success' => true]);
    // }

    public function searchPassenger($mobile)
    {
        $passenger = TicketIssue::where('customer_mobile', $mobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($passenger) {
            return response()->json([
                'status' => true,
                'passenger_name' => $passenger->customer_name,
                'passenger_email' => $passenger->passenger_email,
            ]);
        } else {
            return response()->json(['status' => false, 'message' => 'Passenger not found.'], 404);
        }
    }

    private function initiatePayment($ticket)
    {
        $post_data = [
            'store_id' => env('SSLCZ_STORE_ID'),
            'store_passwd' => env('SSLCZ_STORE_PASSWORD'),
            'total_amount' => $ticket->grand_total,
            'currency' => "BDT",
            'tran_id' => $ticket->transaction_id,
            'success_url' => url('/api/payment/success'),
            'fail_url' => url('/api/payment/fail'),
            'cancel_url' => url('/api/payment/cancel'),
            'cus_name' => $ticket->customer_name,
            'cus_email' => $ticket->passenger_email ?? 'customer@mail.com',
            'cus_phone' => $ticket->customer_mobile,
            'shipping_method' => 'NO',
            'product_name' => 'Bus Ticket',
            'product_category' => 'Ticket',
            'product_profile' => 'non-physical-goods',
        ];

        $api_url = env('SSLCZ_TESTMODE') ? "https://sandbox.sslcommerz.com/gwprocess/v4/api.php" : "https://securepay.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $api_url);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($handle);
        $result = json_decode($response);

        if ($result->status == 'SUCCESS') {
            return response()->json(['status' => true, 'redirect_url' => $result->GatewayPageURL]);
        } else {
            return response()->json(['status' => false, 'message' => 'Payment initiation failed']);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $tran_id = $request->input('tran_id');
        $val_id = $request->input('val_id');

        // পেমেন্ট ভ্যালিডেশন চেক (সিকিউরিটির জন্য)
        if ($this->validatePayment($val_id)) {
            $ticket = TicketIssue::where('transaction_id', $tran_id)->first();
            if ($ticket) {
                $ticket->update(['status_label' => 'Sold']);

                // ইমেইল পাঠানো
                if ($ticket->passenger_email) {
                    $this->sendConfirmationMail($ticket);
                }

                // ফ্রন্টএন্ডে সাকসেস পেজে পাঠানো
                return redirect('http://localhost:3000/booking/success?pnr=' . $ticket->pnr_no);
            }
        }
        return redirect('http://localhost:3000/booking/fail');
    }

    private function validatePayment($val_id)
    {
        // SSLCommerz এর Validation API কল করার লজিক এখানে হবে
        return true; // টেস্ট মুডের জন্য আপাতত true রাখা হলো
    }

    public function paymentFail()
    {
        return redirect('http://localhost:3000/booking/fail');
    }

}
