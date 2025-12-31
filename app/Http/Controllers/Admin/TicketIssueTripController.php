<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Route;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\SeatSold;
use App\Models\Station;
use Illuminate\Support\Facades\Mail;
use App\Models\TicketCancellation;
use App\Models\TicketIssue;
use App\Models\TicketIssueSeat;
use Carbon\Carbon;
use App\Models\SeatLock;
use App\Models\FareStationPrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\LoyaltyDiscount;
use App\Models\SystemSetting;
use App\Mail\TicketMail;
class TicketIssueTripController extends Controller
{
    // Carbon ইমপোর্ট করা হয়েছে, যদি আগে না করা থাকে
    public function index(Request $request)
    {
        $user = Auth::user();
        $settings = SystemSetting::first();

        // --- 1. Date Calculation ---

        // Future advance booking limit (advance_booking)
        $advanceDays = ($settings && $settings->advance_booking > 0) ? (int) $settings->advance_booking : 7;
        // Past schedule viewing limit (previous_date_view_allow)
        $pastDays = ($settings && $settings->previous_date_view_allow > 0) ? (int) $settings->previous_date_view_allow : 0;

        // ✅ NEW/FIXED: Read the seat lock lifetime in seconds.
        // This uses the specified column and defaults to 120 seconds (2 minutes) if data is missing or zero.
        $lockLifetimeSeconds = 120; // Default value
        if ($settings && isset($settings->selected_seat_lifetime) && $settings->selected_seat_lifetime > 0) {
            $lockLifetimeSeconds = (int) $settings->selected_seat_lifetime;
        }

        // Calculate boundaries
        $maxDate = Carbon::now()->addDays($advanceDays)->toDateString();
        $minDate = Carbon::now()->subDays($pastDays)->toDateString(); // Minimum allowed past date

        $requestedDate = $request->date ?? Carbon::now()->toDateString();

        // Bounding the requested date
        $date = $requestedDate;
        if ($date > $maxDate) {
            $date = $maxDate;
        }
        // ✅ FIX: Ensure requested date is not older than the allowed past limit
        if ($date < $minDate) {
            $date = $minDate;
        }
        // ----------------------------

        $fromStation = $request->from_station;
        $toStation = $request->to_station;
        $stationFareList = Station::orderBy('name')->pluck('name')->toArray();
        $fromID = Station::where('name', $fromStation)->value('id');
        $toID = Station::where('name', $toStation)->value('id');
        $fare = FareStationPrice::where('origin_station_id', $fromID)
            ->where('destination_station_id', $toID)
            ->first();

        // 1. Eager Load required relations
        $query = Schedule::with([
            'startStation',
            'endStation',
            'bus',
            'route.routeStationSequences',
            'routeFares',
        ])->whereDate('start_time', $date);

        if ($fromID && $toID) {
            // 🎯 FIX 2: Implement robust segment filtering using sequence_order
            // Get the sequence order of the destination station
            $posTo = DB::table('route_station_sequence')
                ->where('station_id', $toID)
                ->value('sequence_order');

            if ($posTo) {
                // Filter 1: Trip must contain the destination station ($toID)
                $query->whereHas('route.routeStationSequences', function ($q) use ($toID) {
                    $q->where('station_id', $toID);
                });

                // ✅ FIX: Segment Check - Trip must contain $fromID AND its sequence_order must be less than $posTo
                $query->whereHas('route.routeStationSequences', function ($q) use ($fromID, $posTo) {
                    $q->where('station_id', $fromID)
                        ->where('sequence_order', '<', $posTo);
                });

            } else {
                // If destination position is unknown, no valid trips can be shown for the segment
                $trips = collect();
                return view('admin.ticket_issue.index', compact(
                    'trips',
                    'minDate',
                    'maxDate',
                    'fromStation',
                    'toStation',
                    'date',
                    'user',
                    'stationFareList',
                    'fare',
                    'fromID',
                    'toID',
                    'lockLifetimeSeconds',
                ));
            }
        }

        $trips = $query->orderBy('start_time', 'ASC')->get();

        return view('admin.ticket_issue.index', compact(
            'trips',
            'fromStation',
            'toStation',
            'date',
            'user',
            'stationFareList',
            'fare',
            'maxDate',
            'minDate',
            'fromID',
            'toID',
            'lockLifetimeSeconds' // ✅ Passed dynamically to Blade
        ));
    }
    // loadUI is called when a trip row is clicked to show the seat map
    public function loadUI($tripId, $originStationId = null, $destinationStationId = null)
    {


        try {
            $allSettings = SystemSetting::all();
            $maxSeatLimit = $allSettings->pluck('counter_max_seat_per_ticket')->min();
            // 1. Load trip with required relations
            $trip = Schedule::with([
                'startStation',
                'endStation',
                'seat_layout',
                'routeFares.fromStation',
                'routeFares.toStation',
                'bus',
                'route.routeStationSequences',
            ])->findOrFail($tripId);

            // 2. Guarantee $originStationId is set to a valid, non-null ID.
            if (!$originStationId) {
                $originStationId = $trip->start_station_id;
            }
            $user = Auth::user();

            // ইউজারের counter push করুন
            $counters = collect();
            if ($user->counter) {
                $counters->push($user->counter);
            }

            // ইউজারের counter ছাড়া বাকি counter merge করুন
            $otherCounters = Counter::where('id', '!=', $user->counter_id)->orderBy('name', 'ASC')->get();
            $counters = $counters->merge($otherCounters);
            // --- Determine the correct stations for display based on search ---
            $displayOriginName = $trip->startStation?->name ?? 'N/A';
            $displayDestinationName = $trip->endStation?->name ?? 'N/A';
            $displayFare = null;

            if ($originStationId && $destinationStationId) {
                $searchedFare = $trip->routeFares->first(function ($fare) use ($originStationId, $destinationStationId) {
                    $originMatch = ($fare->origin_station_id == $originStationId) || ($fare->from_station_id == $originStationId);
                    $destinationMatch = ($fare->destination_station_id == $destinationStationId) || ($fare->to_station_id == $destinationStationId);
                    return $originMatch && $destinationMatch;
                });

                if ($searchedFare) {
                    $displayOriginName = $searchedFare->fromStation?->name ?? $displayOriginName;
                    $displayDestinationName = $searchedFare->toStation?->name ?? $displayDestinationName;
                    $displayFare = $searchedFare->price ?? null;
                }
            }
            // ----------------------------------------------------------------------

            // 3. Generate fare list
            $stationFareList = $trip->routeFares->map(function ($fare) {
                $originId = $fare->origin_station_id ?? $fare->from_station_id ?? '';
                $destinationId = $fare->destination_station_id ?? $fare->to_station_id ?? '';
                $price = $fare->price ?? 0;
                $originName = $fare->fromStation?->name ?? 'Unknown Origin';
                $destinationName = $fare->toStation?->name ?? 'Unknown Destination';

                return [
                    'value' => $originId . ',' . $destinationId . ',' . $price,
                    'text' => "{$originName} ⟹ {$destinationName} ({$price})",
                ];
            })->toArray();

            // 4. Load other required data
            $startStationId = $trip->start_station_id ?? null;
            $endStationId = $trip->end_station_id ?? null;

            $boardingCounters = $startStationId ? Counter::where('station_id', $startStationId)->orderBy('name', 'ASC')->get() : collect();
            $droppingCounters = $endStationId ? Counter::where('station_id', $endStationId)->orderBy('name', 'ASC')->get() : collect();

            // 5. Load all conflicting tickets (filtering happens in Blade)
            // 🚨 FIX: Eager load issueCounter relation for the Blade snippet (as requested)
            $tickets = TicketIssue::with(['issueCounter'])
                ->where('schedule_id', $tripId)
                ->whereIn('status_label', ['Sold', 'Booked'])
                ->get();
            $activeLocks = $this->getActiveLocks($tripId);

            // 6. Return view with all necessary data
            return view('admin.ticket_issue.inline_ui', compact(
                'trip',
                'boardingCounters',
                'droppingCounters',
                'stationFareList',
                'tickets',
                'activeLocks',
                'originStationId',
                'destinationStationId',
                'displayOriginName',
                'displayDestinationName',
                'displayFare',
                'maxSeatLimit',
                'counters',
                'user',
            ));

        } catch (\Exception $e) {
            // Temporary debug for dev
            return response()->json([
                'error' => 'An error occurred while loading the UI.',
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'def_counter_id' => 'nullable|integer|exists:counters,id',
            'def_counter_master_id' => 'nullable|integer|exists:users,id', // ড্রপডাউন থেকে সিলেক্ট করা ইউজার
            'schedule_id' => 'required|integer|exists:schedules,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_mobile' => 'required|string|max:20',
            'passenger_email' => 'nullable|email|max:255',
            'passenger_gender' => 'nullable|string|in:male,female,other',
            'station_from_to' => 'required|string',
            'boarding_counter_id' => 'required|integer|exists:counters,id',
            'dropping_counter_id' => 'required|integer|exists:counters,id',
            'seats' => 'required|array|min:1',
            'seats.*.seat_number' => 'required|string|max:10',
            'seats.*.fare' => 'required|numeric|min:0',
            'ticket_action' => 'nullable|string|in:book,booked,sold',
            'payment_method' => 'nullable|string|in:cash,online,card',
            'service_charge' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'goods_charge' => 'nullable|numeric|min:0',
            'callerman_commission' => 'nullable|numeric|min:0',
            'journey_date' => 'required|date_format:Y-m-d',
        ]);

        $settings = SystemSetting::first();
        $maxAllowedSeats = $settings->max_seat_per_ticket ?? 4;
        $requestedSeatCount = count($request->seats);

        if ($requestedSeatCount > $maxAllowedSeats) {
            return response()->json([
                'status' => false,
                'message' => "Maximum {$maxAllowedSeats} seats allowed per ticket.",
            ], 422);
        }

        // 2. Parse sub-route and fetch Schedule/Route info
        $stationFromTo = explode(',', $request->station_from_to);

        if (count($stationFromTo) !== 3) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid route selection. Expected format: fromId,toId,fare.',
            ], 422);
        }

        $fromStationId = $stationFromTo[0];
        $toStationId = $stationFromTo[1];
        $fare = $stationFromTo[2];

        // 🚨 ডাইনামিক আইডি সেট করা (লগইন করা ইউজার নয়, ড্রপডাউন থেকে আসা আইডি)
        $issueCounterId = $request->def_counter_id ?? auth()->user()->counter_id;
        $issuedBy = $request->def_counter_master_id ?? auth()->id();

        // চেক করুন কাউন্টার আইডি পাওয়া গেছে কি না
        if (!$issueCounterId) {
            return response()->json(['status' => false, 'message' => 'User counter not assigned!'], 422);
        }

        $schedule = Schedule::with(['route'])->findOrFail($request->schedule_id);
        $routeId = $schedule->route->id ?? null;
        $busType = $schedule->bus_type ?? 'Non-AC';

        DB::beginTransaction();
        try {
            // 4. Calculate Financials
            $subTotal = array_sum(array_column($request->seats, 'fare'));
            $seatCount = count($request->seats);

            // AUTO LOYALTY DISCOUNT CALCULATION
            $autoDiscountAmount = method_exists($this, 'getLoyaltyDiscountAmount')
                ? $this->getLoyaltyDiscountAmount($request->passenger_mobile) : 0;

            $manualDiscountAmount = $request->discount_amount ?? 0;
            $discountAmount = $manualDiscountAmount > 0 ? $manualDiscountAmount : $autoDiscountAmount;

            $serviceCharge = $request->service_charge ?? 0;
            $goodsCharge = $request->goods_charge ?? 0;
            $callermanComm = $request->callerman_commission ?? 0;

            // Counter Commission Calculation based on SELECTED counter
            $counterCommAmount = 0;
            if ($routeId) {
                $commissionData = DB::table('counter_route_commissions')
                    ->where('counter_id', $issueCounterId)
                    ->where('route_id', $routeId)
                    ->first();

                if ($commissionData) {
                    $rateColumn = (strtoupper($busType) === 'AC') ? 'ac_commission' : 'non_ac_commission';
                    $commissionRate = $commissionData->{$rateColumn} ?? 0;
                    $counterCommAmount = $commissionRate * $seatCount;
                }
            }

            $totalCommission = $callermanComm + $counterCommAmount;

            // Grand Total Calculation
            $grandTotal = $subTotal + $serviceCharge + $goodsCharge - $discountAmount - $totalCommission;
            if ($grandTotal < 0) {
                $grandTotal = 0;
            }

            // 5. Seats
            $seatNumbersArray = array_column($request->seats, 'seat_number');

            // 7. Create TicketIssue
            $statusLabel = in_array(strtolower($request->ticket_action), ['book', 'booked', '0']) ? 'Booked' : 'Sold';

            $ticket = TicketIssue::create([
                'schedule_id' => $request->schedule_id,
                'invoice_no' => 'INV-' . time(),
                'pnr_no' => strtoupper(substr(uniqid(), 5)) . rand(100, 999),
                'issue_date' => now(),
                'journey_date' => $request->journey_date,
                'issued_by' => $issuedBy, // সিলেক্ট করা ইউজার আইডি
                'issue_counter_id' => $issueCounterId, // সিলেক্ট করা কাউন্টার আইডি
                'payment_method' => $request->payment_method ?? 'cash',

                'customer_name' => $request->passenger_name,
                'customer_mobile' => $request->passenger_mobile,
                'passenger_email' => $request->passenger_email,
                'gender' => $request->passenger_gender,

                'from_station_id' => $fromStationId,
                'to_station_id' => $toStationId,
                'boarding_counter_id' => $request->boarding_counter_id,
                'dropping_counter_id' => $request->dropping_counter_id,
                'fare' => $fare,

                'seat_numbers' => implode(',', $seatNumbersArray),
                'seats_count' => $seatCount,

                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'is_loyalty_discount_applied' => ($autoDiscountAmount > 0) ? true : false,
                'service_charge' => $serviceCharge,
                'goods_charge' => $goodsCharge,
                'callerman_commission' => $callermanComm,
                'counter_commission_amount' => $counterCommAmount,
                'grand_total' => $grandTotal,

                'status_label' => $statusLabel,
            ]);

            $ticket->update(['ticket_issue_id' => $ticket->id]);

            // 9. CLEAR SEAT LOCKS (সিলেক্ট করা কাউন্টারের লক ক্লিয়ার করা হচ্ছে)
            SeatLock::where('schedule_id', $request->schedule_id)
                ->where('counter_id', $issueCounterId)
                ->whereIn('seat_number', $seatNumbersArray)
                ->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Ticket issued successfully for selected counter.',
                'ticket_id' => $ticket->id,
                'invoice_no' => $ticket->invoice_no,
                'pnr_no' => $ticket->pnr_no,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Ticket Issue Failed: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    // In App\Http\Controllers\Admin\TicketIssueTripController.php

    public function view($id)
    {
        // 1. Fetch the TicketIssue without the 'seats' relationship
        $ticket = \App\Models\TicketIssue::with([
            'route',
            // 'seats' রিলেশনশিপ বাদ দেওয়া হয়েছে
            'fromStation',
            'toStation',
            'schedule.bus',
            'issuedBy',
            'boardingCounter',
            'droppingCounter',
        ])->findOrFail($id);

        // 2. Compute total amount
        $ticket->total_amount = $ticket->grand_total
            ?? ($ticket->sub_total - ($ticket->discount_amount ?? 0) + ($ticket->service_charge ?? 0));

        // 3. Prepare seat list using the 'seat_numbers' string column
        // 🚨 FIX: সরাসরি 'seat_numbers' কলাম থেকে কমা-সেপারেটেড স্ট্রিং নিয়ে অ্যারে তৈরি করা হচ্ছে।
        $seatNumbersString = $ticket->seat_numbers ?? '';

        // স্ট্রিংটিকে কমা দ্বারা ভাগ করে একটি অ্যারে তৈরি করা হলো। 
        // array_filter() খালি স্ট্রিংগুলি (যদি থাকে) বাদ দেবে।
        $seatList = array_filter(explode(',', $seatNumbersString));

        // 4. Render the Blade view
        // এখন Blade-এ $seatList ব্যবহার করা হবে।
        $html = view('admin.ticket_issue.view', compact('ticket', 'seatList'))->render();

        // 5. Return JSON response
        return response()->json([
            'status' => true,
            'html' => $html,
        ]);
    }

    public function cancel($id)
    {
        // টিকেট খোঁজা
        $ticket = TicketIssue::find($id);

        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found',
            ]);
        }

        // যদি টিকেট ইতিমধ্যেই cancelled থাকে
        if ($ticket->status === 'cancelled') {
            return response()->json([
                'status' => false,
                'message' => 'Ticket is already cancelled',
            ]);
        }

        // ট্রানজেকশন শুরু
        DB::beginTransaction();

        try {
            // ১. ক্যানসেলেশন লগ করা
            TicketCancellation::create([
                'ticket_id' => $ticket->id,
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'reason' => 'Cancelled via dashboard',
                'refund_amount' => $ticket->grand_total,
            ]);

            // ২. সিট মুক্ত করা (Single Query Fix)
            // 🚨 ফিক্স: এই টিকেটের সাথে যুক্ত সকল সিটকে একটি মাত্র কোয়েরির মাধ্যমে মুক্ত করা হলো।
            $seatsReleased = SeatSold::where('schedule_id', $ticket->schedule_id)
                ->where('ticket_issue_id', $ticket->id) // শুধুমাত্র এই টিকেটের সিটগুলো
                ->update([
                    'status' => 'available', // সিটকে `available` করা
                    'ticket_issue_id' => null, // টিকেট থেকে সিটটি মুক্ত করা
                ]);

            // 💡 যদি আপনার লজিক SeatSold এন্ট্রি ডিলিট করে, তবে উপরের update এর পরিবর্তে এটি ব্যবহার করুন:
            // $seatsReleased = SeatSold::where('ticket_issue_id', $ticket->id)->delete();

            // ৩. টিকেট স্ট্যাটাস `cancelled` করা
            $ticket->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'status_label' => 'Cancelled', // status_label কলাম থাকলে এটিও আপডেট করুন
            ]);

            // ট্রানজেকশন কমিট
            DB::commit();

            // সফল ক্যানসেল হওয়ার পর সাড়া
            return response()->json([
                'status' => true,
                'message' => "Ticket cancelled successfully.",
            ]);
        } catch (Exception $e) {
            // কিছু ভুল হলে ট্রানজেকশন রোলব্যাক
            DB::rollBack();

            // ডিবাগিং এর জন্য লগ
            \Log::error('Ticket Cancellation Error: ' . $e->getMessage() . ' for ticket ID: ' . $id);

            return response()->json([
                'status' => false,
                'message' => 'Error canceling ticket. See logs for details. ' . $e->getMessage(),
            ], 500);
        }
    }





    // public function cancelSeats(Request $req)
    // {
    //     $req->validate([
    //         'ticket_id' => 'required',
    //         'seats' => 'required|array',
    //     ]);

    //     $ticket = TicketIssue::with('seats')->find($req->ticket_id);

    //     if (!$ticket) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Ticket not found',
    //         ]);
    //     }

    //     foreach ($req->seats as $seatId) {
    //         $seat = TicketIssueSeat::find($seatId);
    //         if ($seat) {
    //             $seat->update(['is_cancelled' => 1]);

    //             // Free the seat
    //             \App\Models\SeatSold::where('schedule_id', $ticket->schedule_id)
    //                 ->where('seat_number', $seat->seat_number)
    //                 ->delete();
    //         }
    //     }

    //     // Optional: If all seats are cancelled, mark ticket fully cancelled
    //     $remainingSeats = $ticket->seats()->where('is_cancelled', 0)->count();
    //     if ($remainingSeats === 0) {
    //         $ticket->update([
    //             'status' => 'cancelled',
    //             'cancelled_by' => auth()->id(),
    //             'cancelled_at' => now(),
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => "Selected seats cancelled successfully",
    //     ]);
    // }




    public function getSeatLayout(Request $request)
    {
        $scheduleId = $request->schedule_id;
        $date = $request->date;  // You can pass the date from the frontend

        // Fetch the tickets for the given schedule and date
        $seats = TicketIssue::where('schedule_id', $scheduleId)
            ->whereDate('issue_date', $date)  // Filter by the issue date
            ->get();

        $seatLayout = [];

        foreach ($seats as $seat) {
            $status = 'available';  // Default status

            // If ticket is sold on the selected date, mark seat as 'sold'
            if ($seat->status_label === 'Sold') {
                $status = 'sold';
            }

            $seatLayout[] = [
                'seat_number' => $seat->seat_number,
                'status' => $status,
                'passenger_name' => $seat->customer_name,
                'passenger_mobile' => $seat->customer_mobile,
            ];
        }

        return response()->json([
            'status' => true,
            'layout' => $seatLayout,
        ]);
    }

    public function tripSheet($id)
    {
        // 1. Load Trip Data
        $trip = Schedule::with(['bus', 'route', 'startStation', 'endStation', 'seat_layout'])
            ->findOrFail($id);

        // 2. Load All Tickets (Booked or Sold)
        $tickets = TicketIssue::where('schedule_id', $id)
            ->with(['boardingCounter', 'droppingCounter'])
            ->get();

        // 3. Calculate Summaries
        $soldCount = $tickets->where('status_label', 'Sold')->count();
        $bookedCount = $tickets->where('status_label', 'Booked')->count();
        $totalSeats = $trip->seat_layout->total_seats ?? 0;
        $availableSeats = $totalSeats - ($soldCount + $bookedCount);

        // Calculate total fare for sold tickets only
        $soldTotalFare = $tickets->where('status_label', 'Sold')->sum('grand_total');
        $bookedTotalFare = $tickets->where('status_label', 'Booked')->sum('grand_total'); // You may use this later if needed.

        // 4. Render the Trip Sheet View
        $html = view('admin.ticket_issue.trip_sheet', compact(
            'trip',
            'tickets',
            'soldCount',
            'bookedCount',
            'availableSeats',
            'totalSeats',
            'soldTotalFare',  // Passing sold fare total
            'bookedTotalFare' // Passing booked fare total (if you want to display it separately)
        ))->render();

        return response()->json(['status' => true, 'html' => $html]);
    }
    public function engageSeat(Request $request)
    {
        // ... (Validation and variable setup are fine) ...

        $scheduleId = $request->schedule_id;
        $seatNumber = $request->seat_number;
        $counterId = Auth::user()->counter_id ?? null; // Ensure this is the correct column name

        // ... (Sold/Booked check is fine) ...

        // 2. Check for existing active lock
        $existingLock = SeatLock::where('schedule_id', $scheduleId)
            ->where('seat_number', $seatNumber)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingLock) {
            // SCENARIO A: A lock already exists

            // If locked by ANOTHER counter
            if ($existingLock->counter_id != $counterId) {
                return response()->json(['success' => false, 'message' => 'Seat is temporarily locked by another counter.'], 423);
            }

            // If locked by the SAME counter, just update the expiration time (extend the lock)
            // **This logic correctly prevents the creation step if the lock is successfully extended.**
            $existingLock->update(['expires_at' => now()->addMinutes(5)]);
            return response()->json(['success' => true, 'action' => 'extended']);
        }

        // SCENARIO B: NO active lock found. Proceed to create a new lock.
        SeatLock::create([
            'schedule_id' => $scheduleId,
            'seat_number' => $seatNumber,
            'counter_id' => $counterId,
            'expires_at' => now()->addMinutes(5),
        ]);

        return response()->json(['success' => true, 'action' => 'locked']);
    }

    public function releaseSeat(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_number' => 'required|string',
        ]);

        // Delete the lock created by the current user/counter for this seat
        SeatLock::where('schedule_id', $request->schedule_id)
            ->where('seat_number', $request->seat_number)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function getActiveLocks($scheduleId)
    {
        // Fetch all active locks for a schedule, including the Counter model
        return SeatLock::with('counter') // <--- ADDED COUNTER RELATION
            ->where('schedule_id', $scheduleId)
            ->where('expires_at', '>', now())
            ->get();

    }

    protected function hasSegmentConflict(
        $scheduleId,
        array $seatNumbersArray,
        $userFromStationId,
        $userToStationId,
        array $routeSequences,
    ) {
        $userFromSeq = $routeSequences[$userFromStationId] ?? 0;
        $userToSeq = $routeSequences[$userToStationId] ?? 999;

        $conflictingTickets = TicketIssue::where('schedule_id', $scheduleId)
            ->whereIn('status_label', ['Sold', 'Booked'])
            ->where(function ($query) use ($seatNumbersArray) {
                foreach ($seatNumbersArray as $seat) {
                    $query->orWhere('seat_numbers', 'LIKE', "%$seat%");
                }
            })
            ->get();

        foreach ($conflictingTickets as $ticket) {
            $ticketFromSeq = $routeSequences[$ticket->from_station_id] ?? 0;
            $ticketToSeq = $routeSequences[$ticket->to_station_id] ?? 999;

            // Segment overlap check: [A, B] and [C, D] overlap if A < D and C < B
            if ($ticketFromSeq < $userToSeq && $ticketToSeq > $userFromSeq) {
                return true; // Conflict found
            }
        }
        return false; // No conflict
    }

    public function createMultiTicketSale(Request $request)
    {
        $tripId = $request->trip_id;
        $seats = $request->seats; // Array of selected seats for each passenger
        $customerDetails = $request->customers; // Array of customer details

        // Check for seat availability
        $conflictingSeats = [];
        foreach ($seats as $seat) {
            if ($this->isSeatTaken($tripId, $seat)) {
                $conflictingSeats[] = $seat;
            }
        }

        if (count($conflictingSeats) > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'The following seats are already booked: ' . implode(', ', $conflictingSeats)
            ]);
        }

        // Process the sale for each passenger
        $tickets = [];
        foreach ($customerDetails as $index => $customer) {
            $ticket = new TicketIssue();
            $ticket->trip_id = $tripId;
            $ticket->seat_number = $seats[$index];
            $ticket->customer_name = $customer['name'];
            $ticket->customer_mobile = $customer['mobile'];
            $ticket->fare = 200; // Example fare
            $ticket->status = 'sold';
            $ticket->save();

            $tickets[] = $ticket;
        }

        // Process payment (simplified for this example)
        $totalAmount = count($tickets) * 200; // Example total fare calculation

        // Assume payment is successful, then finalize
        return response()->json([
            'status' => 'success',
            'message' => 'Tickets successfully booked.',
            'tickets' => $tickets,
        ]);
    }

    public function isSeatTaken($tripId, $seatNumber)
    {
        // Check if the seat is already taken for the given trip
        return TicketIssue::where('trip_id', $tripId)
            ->where('seat_number', $seatNumber)
            ->exists();
    }

    public function isSeatAvailable($seatList, $scheduleId, $start, $end, $date)
    {
        return DB::table('ticket_issues')
            ->where('schedule_id', $scheduleId)
            ->where('journey_date', $date)
            ->where('status', 'active')
            ->where(function ($q) use ($seatList) {
                foreach ($seatList as $seat) {
                    $q->orWhere('seat_numbers', 'LIKE', "%$seat%");
                }
            })
            ->where(function ($q) use ($start, $end) {
                $q->where('leg_start_station_id', '<', $end)
                    ->where('leg_end_station_id', '>', $start);
            })
            ->doesntExist();
    }

    public function issueTicket(Request $req)
    {
        $seatList = explode(',', $req->seat_numbers);

        // Seat availability check
        $available = $this->isSeatAvailable(
            $seatList,
            $req->schedule_id,
            $req->leg_start_station_id,
            $req->leg_end_station_id,
            $req->journey_date,
        );

        if (!$available) {
            return response()->json(['status' => false, 'message' => 'Seat not available'], 422);
        }

        $ticket = TicketIssue::create([
            'ticket_issue_id' => null,
            'schedule_id' => $req->schedule_id,
            'from_station_id' => $req->from_station_id,
            'to_station_id' => $req->to_station_id,
            'boarding_counter_id' => $req->boarding_counter_id,
            'dropping_counter_id' => $req->dropping_counter_id,

            'customer_name' => $req->customer_name,
            'customer_mobile' => $req->customer_mobile,
            'gender' => $req->gender,

            'seat_numbers' => $req->seat_numbers,
            'seats_count' => count($seatList),

            'fare' => $req->fare,
            'sub_total' => $req->fare * count($seatList),
            'discount_amount' => $req->discount_amount ?? 0,
            'service_charge' => $req->service_charge ?? 0,
            'goods_charge' => $req->goods_charge ?? 0,
            'callerman_commission' => $req->callerman_commission ?? 0,

            'grand_total' => $req->grand_total,

            'invoice_no' => uniqid('INV-'),
            'pnr_no' => strtoupper(\Str::random(10)),
            'issue_date' => now(),
            'payment_method' => $req->payment_method,

            // segment logic
            'leg_start_station_id' => $req->leg_start_station_id,
            'leg_end_station_id' => $req->leg_end_station_id,

            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return response()->json(['status' => true, 'ticket' => $ticket]);
    }


    public function getSeatsByRoute($fromTo)
    {
        // ১. চেক করুন ফরম্যাট ঠিক আছে কি না (e.g., 1-2)
        if (!str_contains($fromTo, '-')) {
            return response()->json(['status' => false, 'message' => 'Invalid route format. Use fromID-toID.'], 400);
        }

        // ২. আইডিগুলো আলাদা করা
        $parts = explode('-', $fromTo);
        $fromId = $parts[0] ?? null;
        $toId = $parts[1] ?? null;
        $tripId = request()->query('trip_id');

        if (!$fromId || !$toId || !$tripId) {
            return response()->json(['status' => false, 'message' => 'Missing Station or Trip IDs.'], 422);
        }

        // ৩. মেইন সিট লজিক কল করা
        return $this->getSeatsResponse($tripId, $fromId, $toId);
    }


    public function getSeatsBySchedule($scheduleId)
    {
        return $this->getSeatsResponse($scheduleId, null, null);
    }

    public function getSeatsByScheduleFromTo($scheduleId, $from, $to)
    {
        return $this->getSeatsResponse($scheduleId, $from, $to);
    }

    /**
     * Core helper: Returns seat status based on segment overlap and active locks.
     */
    protected function getSeatsResponse($scheduleId, $userFromStationId = null, $userToStationId = null)
    {
        try {
            // ১. ট্রিপ এবং রুট সিকোয়েন্স লোড করা
            $schedule = Schedule::with(['seat_layout', 'route.routeStationSequences'])->find($scheduleId);

            if (!$schedule || !$schedule->route || !$schedule->route->routeStationSequences) {
                return response()->json(['status' => false, 'message' => 'Trip or Route Sequence Data missing.']);
            }

            // ২. ট্রিপের তারিখ বের করা (Carbon ব্যবহার করে)
            $journeyDate = \Carbon\Carbon::parse($schedule->start_time)->toDateString();

            $layout = $schedule->seat_layout;
            $rows = $layout->rows ?? 4;
            $cols = $layout->columns ?? 4;

            // ৩. রুট সিকোয়েন্স ম্যাপ তৈরি (Station ID => Sequence Order)
            $routeSequences = $schedule->route->routeStationSequences
                ->pluck('sequence_order', 'station_id')
                ->map(fn($q) => (int) $q)
                ->toArray();

            // ৪. বর্তমান ইউজারের সিলেক্ট করা সেকশনের অর্ডার (যেমন: ঢাকা-কুমিল্লা হলে ১-২)
            $userFromSeq = $userFromStationId ? ($routeSequences[$userFromStationId] ?? 0) : 0;
            $userToSeq = $userToStationId ? ($routeSequences[$userToStationId] ?? 999) : 999;

            // ৫. ঐ তারিখের সব Sold/Booked টিকেট নিয়ে আসা
            $occupiedTickets = TicketIssue::with(['issueCounter'])
                ->where('schedule_id', $scheduleId)
                ->whereDate('journey_date', $journeyDate)
                ->whereIn('status_label', ['Sold', 'Booked'])
                ->get();

            // ৬. একটিভ লক সিট (অন্য কাউন্টার থেকে ব্লক করা)
            $activeLocks = $this->getActiveLocks($scheduleId);
            $lockedSeats = $activeLocks->pluck('counter_id', 'seat_number')->toArray();

            // ৭. সিট গ্রিড তৈরি
            $seats = [];
            for ($r = 1; $r <= $rows; $r++) {
                for ($c = 1; $c <= $cols; $c++) {
                    $seatNo = chr(64 + $r) . $c; // ভেরিয়েবল নাম ফিক্স করা হয়েছে
                    $seatStatus = 'available';
                    $ticketId = null;
                    $gender = 'male';
                    $customerName = null;
                    $counterName = null;

                    // এ. ইন্টারলকিং ওভারল্যাপ চেক (সবচেয়ে গুরুত্বপূর্ণ অংশ)
                    foreach ($occupiedTickets as $ticket) {
                        $bookedSeats = explode(',', $ticket->seat_numbers ?? '');

                        if (in_array($seatNo, $bookedSeats)) {
                            $ticketFromSeq = $routeSequences[$ticket->from_station_id] ?? 0;
                            $ticketToSeq = $routeSequences[$ticket->to_station_id] ?? 999;

                            /**
                             * 🚨 ইন্টারলকিং রুল (Overlap Rule):
                             * টিকেট তখনই বুকড দেখাবে যদি: (টিকেট_শুরু < ইউজার_শেষ) এবং (টিকেট_শেষ > ইউজার_শুরু) হয়।
                             */
                            $overlap = ($ticketFromSeq < $userToSeq && $ticketToSeq > $userFromSeq);

                            if ($overlap) {
                                $seatStatus = (strtolower($ticket->status_label) === 'sold') ? 'sold' : 'booked';
                                $ticketId = $ticket->id;
                                $gender = $ticket->gender ?? 'male';
                                $customerName = $ticket->customer_name;
                                $counterName = $ticket->issueCounter?->name ?? 'N/A';
                                break;
                            }
                        }
                    }

                    // বি. লক চেক (যদি ওভারল্যাপ না থাকে)
                    if ($seatStatus === 'available' && isset($lockedSeats[$seatNo])) {
                        $seatStatus = 'engaged';
                    }

                    $seats[] = [
                        'seat_number' => $seatNo,
                        'status' => $seatStatus,
                        'ticket_id' => $ticketId,
                        'gender' => $gender,
                        'customer_name' => $customerName,
                        'counter_name' => $counterName,
                    ];
                }
            }

            return response()->json(['status' => true, 'seats' => $seats]);

        } catch (\Exception $e) {
            \Log::error("GET SEATS ERROR: " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'PHP Error: ' . $e->getMessage()], 500);
        }
    }
    private function getLoyaltyDiscountAmount(string $mobile): float
    {
        $discountAmount = 0.00;

        // 1. শেষ কেনা টিকিটের তারিখ খুঁজুন
        $lastTicket = TicketIssue::where('customer_mobile', $mobile)
            ->whereIn('status_label', ['Sold', 'Booked'])
            ->orderByDesc('created_at')
            ->first();

        if ($lastTicket) {
            $lastPurchaseDate = Carbon::parse($lastTicket->created_at);
            $today = Carbon::now();
            $daysDifference = $lastPurchaseDate->diffInDays($today);

            // 2. সক্রিয় এবং বর্তমান সময়ে চলমান ডিসকাউন্ট রুলস
            $rules = LoyaltyDiscount::where('is_active', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->orderByDesc('discount_amount') // সর্বোচ্চ ডিসকাউন্ট আগে
                ->get();

            // 3. রুলসগুলির সাথে দিন সংখ্যার পার্থক্য তুলনা করুন
            foreach ($rules as $rule) {
                if ($daysDifference <= $rule->days_threshold) {
                    $discountAmount = $rule->discount_amount;
                    break; // সর্বোচ্চ প্রযোজ্য rule apply করে লুপ বন্ধ
                }
            }
        }

        return $discountAmount;
    }


    public function getPassengerInfo(Request $request)
    {
        // FIX: নিশ্চিত করুন যে এখানে কোনো ডেটাবেস লুকআপ বা মডেল ব্যবহার করা হচ্ছে না, 
        // কারণ 500 ত্রুটিটি সম্ভবত কোনো অনুপস্থিত মডেলের কারণে আসছে।
        // যদি মোবাইল নম্বর না থাকে, শূন্য ডিসকাউন্ট রিটার্ন করুন।
        if (!$request->mobile) {
            return response()->json(['status' => false, 'name' => '', 'message' => 'Mobile number required.']);
        }

        // বর্তমানে শুধুমাত্র ডামি রেসপন্স দেওয়া হলো। 
        // আপনি পরবর্তীতে এখানে আপনার Customer/Passenger মডেল থেকে ডেটাবেস লুকআপ লজিক যোগ করবেন।
        return response()->json([
            'status' => true,
            'name' => 'Dummy Passenger Name',
            'gender' => 'male',
            'message' => 'Dummy passenger info loaded.',
        ]);
    }

    /**
     * মোবাইল নম্বর অনুযায়ী কাস্টমারের লয়ালটি ডিসকাউন্ট চেক করে।
     * ফ্রন্টএন্ড থেকে /admin/passengers/check-loyalty রুটে কল করা হয়
     */
    public function checkLoyaltyDiscount(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|max:20',
        ]);

        $mobile = $request->mobile;
        $discountAmount = 0.00;

        // 1. শেষ কেনা টিকিটের তারিখ খুঁজুন
        $lastTicket = TicketIssue::where('customer_mobile', $mobile)
            ->whereIn('status_label', ['Sold', 'Booked'])
            ->orderByDesc('created_at')
            ->first();

        if ($lastTicket) {
            $lastPurchaseDate = Carbon::parse($lastTicket->created_at);
            $today = Carbon::now();
            $daysDifference = $lastPurchaseDate->diffInDays($today);

            // 2. সক্রিয় এবং চলমান ডিসকাউন্ট রুলস
            $rules = LoyaltyDiscount::where('is_active', 1)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->orderByDesc('discount_amount') // সর্বোচ্চ ডিসকাউন্ট আগে
                ->get();

            // 3. রুলসগুলির সাথে দিন সংখ্যার পার্থক্য তুলনা করুন
            foreach ($rules as $rule) {
                if ($daysDifference <= $rule->days_threshold) {
                    $discountAmount = $rule->discount_amount;
                    break; // সর্বোচ্চ প্রযোজ্য rule
                }
            }
        }

        return response()->json([
            'status' => true,
            'discount_amount' => $discountAmount,
            'message' => $discountAmount > 0 ? 'Loyalty discount found.' : 'No loyalty discount applied.'
        ]);
    }


    // public function sendSms(Request $request, $invoiceId)
    // {
    //     // 1. Fetch Ticket Data
    //     $ticket = TicketIssue::where('invoice_no', 'INV-' . $invoiceId)
    //         ->orWhere('invoice_no', $invoiceId)
    //         ->with(['fromStation', 'toStation', 'schedule'])
    //         ->first();

    //     if (!$ticket || !$ticket->customer_mobile) {
    //         return response()->json(['status' => false, 'message' => 'Ticket or mobile number not found.'], 404);
    //     }

    //     // 2. Get API Configuration (using the 'generic' provider)
    //     $config = config('services.sms.generic');

    //     if (!$config || !isset($config['url'])) {
    //         return response()->json(['status' => false, 'message' => 'SMS Provider configuration is missing.'], 500);
    //     }

    //     // 3. Prepare SMS Content
    //     $mobileNumber = $ticket->customer_mobile;
    //     $startTime = $ticket->schedule?->start_time;

    //     $departureTime = $startTime ? date('h:i A', strtotime($startTime)) : 'N/A';
    //     $journeyDate = $ticket->journey_date ? date('j M Y', strtotime($ticket->journey_date)) : 'N/A';

    //     $message = "Your ticket confirmed! PNR: {$ticket->pnr_no}. Seats: {$ticket->seat_numbers}. Route: {$ticket->fromStation?->name} to {$ticket->toStation?->name}. Dep: {$journeyDate} {$departureTime}. Total: {$ticket->grand_total} Tk. Thanks.";

    //     // 4. Call SMS API (GET request for sendmysms.net)
    //     try {
    //         // 🚨 FIX APPLIED HERE: Renaming parameters to match the required API documentation:
    //         // 'username' -> 'user'
    //         // 'password' -> 'key'
    //         // 'mobileno' -> 'to'
    //         // 'message'  -> 'msg'

    //         $response = Http::get($config['url'], [
    //             'user' => $config['username'],  // API requires 'user'
    //             'key' => $config['password'],   // API requires 'key' (your API-Key)
    //             'to' => $mobileNumber,          // API requires 'to'
    //             'msg' => urlencode($message),   // API requires 'msg'

    //             // Removed redundant/incorrect parameters: 'sourceid' and 'mobileno'/'message' aliases.
    //         ]);

    //         $apiResponse = $response->body();

    //         // 5. Handle API Response
    //         // The success status is strictly "OK" as per the documentation.
    //         if (str_contains($apiResponse, '"status":"OK"')) {

    //             Log::info("SMS sent via sendmysms.net to {$mobileNumber}. Response: {$apiResponse}");

    //             return response()->json([
    //                 'status' => true,
    //                 'message' => "SMS sent successfully to {$mobileNumber}. API Status: OK.",
    //             ]);
    //         } else {

    //             Log::warning("SMS failed via sendmysms.net to {$mobileNumber}. Response: {$apiResponse}");

    //             // Try to extract the specific error response for a better user message
    //             $responseArray = json_decode($apiResponse, true);
    //             $errorResponse = $responseArray['response'] ?? substr($apiResponse, 0, 50) . "...";

    //             return response()->json([
    //                 'status' => false,
    //                 'message' => "SMS failed. Error: {$errorResponse}",
    //             ]);
    //         }

    //     } catch (\Exception $e) {

    //         Log::error('SMS API Connection Error: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Connection error with SMS gateway.',
    //         ], 500);
    //     }
    // }


    public function sendEmail(Request $request, $invoiceId)
    {
        // 1. Fetch Ticket Data
        $ticket = TicketIssue::where('invoice_no', 'INV-' . $invoiceId)
            ->orWhere('invoice_no', $invoiceId)
            ->with(['fromStation', 'toStation', 'schedule', 'schedule.bus'])
            ->first();

        if (!$ticket || !$ticket->passenger_email) { // Assuming customer_email exists on the model
            return response()->json(['status' => false, 'message' => 'Ticket not found or customer email is missing.'], 404);
        }

        // 2. Prepare Data and Send Email
        try {
            // Send the Mailable class with the ticket object
            Mail::to($ticket->passenger_email)->send(new TicketMail($ticket));

            Log::info("Ticket email sent successfully for Invoice: {$ticket->invoice_no} to {$ticket->customer_email}");

            return response()->json([
                'status' => true,
                'message' => "Ticket sent successfully via email to {$ticket->customer_email}.",
            ]);

        } catch (\Exception $e) {
            Log::error('Ticket Email Sending Error: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Failed to send email. Check mail configuration (LOG, SMTP, etc.).',
            ], 500);
        }
    }

    public function search($mobile)
    {
        if (!$mobile) {
            return response()->json(['status' => 'not_found']);
        }

        $ticket = TicketIssue::where('customer_mobile', $mobile)
            ->latest()
            ->first();

        if ($ticket) {
            return response()->json([
                'status' => 'success',
                'name' => $ticket->customer_name,
            ]);
        }

        return response()->json(['status' => 'not_found']);
    }

    public function sendSms(Request $request, $invoiceId)
    {
        // 1. Fetch Ticket Data
        $ticket = TicketIssue::where('invoice_no', 'INV-' . $invoiceId)
            ->orWhere('invoice_no', $invoiceId)
            ->with(['fromStation', 'toStation', 'schedule'])
            ->first();

        if (!$ticket || !$ticket->customer_mobile) {
            return response()->json(['status' => false, 'message' => 'Ticket or mobile number not found.'], 404);
        }

        // 2. Get API Configuration
        $config = config('services.sms.generic');

        // Check configuration completeness (now includes a check for the actual API key value)
        if (!$config || !isset($config['url']) || empty($config['username'])) { // Check if 'username' (API Key) is empty
            return response()->json(['status' => false, 'message' => 'SMS Provider configuration is missing or incomplete (API Key missing).'], 500);
        }

        // 3. Prepare SMS Content and Sanitize Number
        $mobileNumber = $ticket->customer_mobile;

        // A. Remove all non-numeric and non-comma characters
        $mobileNumber = preg_replace('/[^0-9,]/', '', $mobileNumber);

        // B. Ensure single number format is 8801XXXXXXXXXX if currently 01XXXXXXXXXX
        // This logic only applies if there is no comma (single recipient)
        if (strpos($mobileNumber, ',') === false && substr($mobileNumber, 0, 1) === '0' && strlen($mobileNumber) === 11) {
            $mobileNumber = '88' . $mobileNumber;
        }

        // C. Added check: If number becomes empty after sanitization
        if (empty($mobileNumber)) {
            return response()->json(['status' => false, 'message' => 'Mobile number is invalid or became empty after sanitization.'], 400);
        }

        $startTime = $ticket->schedule?->start_time;
        $departureTime = $startTime ? date('h:i A', strtotime($startTime)) : 'N/A';
        $journeyDate = $ticket->journey_date ? date('j M Y', strtotime($ticket->journey_date)) : 'N/A';

        $message = "Your ticket confirmed! PNR: {$ticket->pnr_no}. Seats: {$ticket->seat_numbers}. Route: {$ticket->fromStation?->name} to {$ticket->toStation?->name}. Dep: {$journeyDate} {$departureTime}. Total: {$ticket->grand_total} Tk. Thanks.";

        // Define sender_id (optional)
        $senderId = $config['source_id'] ?? null;

        // 4. Call SMS API
        try {
            $parameters = [
                'api_key' => $config['username'],
                'to' => $mobileNumber,
                'msg' => urlencode($message),
            ];

            if ($senderId) {
                $parameters['sender_id'] = $senderId;
            }

            $response = Http::timeout(10)->get('https://api.sms.net.bd/sendsms', $parameters);
            $apiResponse = $response->body();

            // 5. Handle API Response
            if ($response->successful()) {
                $responseArray = json_decode($apiResponse, true);

                // Check for API success (error code 0)
                if (isset($responseArray['error']) && $responseArray['error'] == 0) {
                    Log::info("SMS sent via sms.net.bd to {$mobileNumber}. Response: {$apiResponse}");

                    return response()->json([
                        'status' => true,
                        'message' => "SMS sent successfully to {$mobileNumber}. API Status: Success.",
                        'details' => $responseArray,
                    ]);
                } else {
                    // Check if a specific error message ('msg') is returned by the API
                    $errorMsg = $responseArray['msg'] ?? $responseArray['response'] ?? 'Unknown API Error';
                    $errorCode = $responseArray['error'] ?? 'N/A';

                    Log::warning("SMS failed via sms.net.bd to {$mobileNumber}. Code: {$errorCode}, Response: {$apiResponse}");

                    return response()->json([
                        'status' => false,
                        'message' => "SMS failed. Error Code {$errorCode}: {$errorMsg}",
                    ], 400);
                }
            } else {
                // HTTP Connection error (e.g., 404, 500, timeout)
                Log::error("SMS failed due to HTTP error ({$response->status()}) to {$mobileNumber}. Response: {$apiResponse}");

                return response()->json([
                    'status' => false,
                    'message' => 'SMS gateway returned an HTTP error.',
                    'http_status' => $response->status(),
                ], 500);
            }

        } catch (\Exception $e) {
            // Connection exception (e.g., network down)
            Log::error('SMS API Connection Exception: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Connection error with SMS gateway.',
            ], 500);
        }
    }

    public function convertToSale(Request $request)
    {
        try {
            $ticketId = $request->ticket_id;

            // টিকেটটি খুঁজে বের করা
            $ticket = TicketIssue::findOrFail($ticketId);

            // শুধুমাত্র বুকিং টিকেটকে বিক্রিতে রূপান্তর করা হবে
            if ($ticket->ticket_action === 'booked' || $ticket->status_label === 'Booked') {
                $ticket->update([
                    'ticket_action' => 'sold',
                    'status_label' => 'Sold',
                    'updated_at' => now(),
                    // প্রয়োজনে এখানে পেমেন্ট রিলেটেড আরও ডাটা আপডেট করতে পারেন
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Ticket successfully converted to SALE!',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'This ticket is not in booked status.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}