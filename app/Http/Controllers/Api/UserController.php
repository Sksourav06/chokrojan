<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Passenger; // User এর বদলে Passenger ব্যবহার করা হয়েছে

class UserController extends Controller
{
    /**
     * লগইন করা প্যাসেঞ্জারের প্রোফাইল তথ্য দেখানো
     */
    public function profile()
    {
        // Sanctum ব্যবহার করলে auth()->user() প্যাসেঞ্জার অবজেক্ট দিবে
        $passenger = auth()->user();

        if (!$passenger) {
            return response()->json(['status' => false, 'message' => 'Passenger not found'], 404);
        }

        return response()->json([
            'status' => true,
            'user' => $passenger, // ফ্রন্টএন্ডের সাথে মিল রাখতে কী 'user' রাখা হয়েছে
        ]);
    }

    /**
     * প্রোফাইল আপডেট করা
     */
    public function update(Request $request)
    {
        $passenger = auth()->user();

        if (!$passenger) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        // ভ্যালিডেশন (প্যাসেঞ্জার টেবিল অনুযায়ী)
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|email|unique:passengers,email,' . $passenger->id,
            'mobile_number' => 'required|string',
        ]);

        // ডেটা আপডেট
        $passenger->update($request->only([
            'first_name',
            'last_name',
            'mobile_number',
            'email',
            'city',
            'zip_code',
            'gender',
            'street_address',
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Profile Updated Successfully',
            'user' => $passenger->fresh(),
        ]);
    }

    /**
     * পাসওয়ার্ড পরিবর্তন করা
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $passenger = auth()->user();

        // বর্তমান পাসওয়ার্ড চেক
        if (!Hash::check($request->current_password, $passenger->password)) {
            return response()->json(['status' => false, 'message' => 'বর্তমান পাসওয়ার্ডটি সঠিক নয়'], 401);
        }

        $passenger->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['status' => true, 'message' => 'Password updated successfully']);
    }

    /**
     * বুকিং হিস্ট্রি দেখানো
     */
    public function myBookings(Request $request)
    {
        $passenger = auth()->user();

        if (!$passenger) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $mobile = $passenger->mobile_number;
        if (!$mobile) {
            return response()->json(['status' => true, 'bookings' => []]);
        }

        // মোবাইল নম্বর ফরম্যাটিং (১১ ডিজিট নিশ্চিত করা)
        $cleanMobile = ltrim($mobile, '+88');
        if (strlen($cleanMobile) == 10) {
            $cleanMobile = '0' . $cleanMobile;
        }

        $bookings = DB::table('ticket_issues')
            // schedules টেবিলের সাথে জয়েন (ঐচ্ছিক কিন্তু নিরাপদ)
            ->leftJoin('schedules', 'ticket_issues.schedule_id', '=', 'schedules.id')
            // ticket_issues টেবিলের সরাসরি station id ব্যবহার করে জয়েন করা হচ্ছে
            ->leftJoin('stations as from_st', 'ticket_issues.from_station_id', '=', 'from_st.id')
            ->leftJoin('stations as to_st', 'ticket_issues.to_station_id', '=', 'to_st.id')
            ->where(function ($query) use ($cleanMobile) {
                $query->where('ticket_issues.customer_mobile', $cleanMobile)
                    ->orWhere('ticket_issues.customer_mobile', '+88' . $cleanMobile);
            })
            ->select(
                'ticket_issues.*',
                'from_st.name as from_station_name', // এই নামগুলো ব্লেড বা ম্যাপে যাবে
                'to_st.name as to_station_name',
            )
            ->orderBy('ticket_issues.created_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                // ডাটাবেজ থেকে নাম না পাওয়া গেলে 'Unknown' দেখাবে
                $from = $ticket->from_station_name ?? 'Unknown';
                $to = $ticket->to_station_name ?? 'Unknown';

                return [
                    'route' => $from . ' to ' . $to, // এখানে N/A আসার চান্স আর নেই
                    'bus_type' => $ticket->bus_type ?? 'Bus',
                    'operator' => 'BD Tickets',
                    'booking_id' => $ticket->pnr_no,
                    'booked_by' => $ticket->customer_name,
                    'total_fare' => $ticket->grand_total,
                    'booking_date' => date('d-M-Y', strtotime($ticket->created_at)),
                    'booking_day' => date('l', strtotime($ticket->created_at)),
                    'journey_date' => $ticket->journey_date ? date('d-M-Y', strtotime($ticket->journey_date)) : 'N/A',
                    'journey_day' => $ticket->journey_date ? date('l', strtotime($ticket->journey_date)) : 'N/A',
                    'status' => strtoupper($ticket->status_label ?? $ticket->status ?? 'PAID'),
                    'seat_numbers' => $ticket->seat_numbers ?? 'N/A',
                    'fare' => $ticket->fare ?? 0,
                    'payment_method' => $ticket->payment_method ?? 'Online',
                ];
            });

        return response()->json([
            'status' => true,
            'bookings' => $bookings,
        ]);
    }
}