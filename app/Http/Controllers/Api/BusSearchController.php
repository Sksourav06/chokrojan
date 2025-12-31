<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Station;
use App\Models\TicketIssue;
use App\Models\SeatLock;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusSearchController extends Controller
{
    /**
     * বাসের তালিকা সার্চ করা (Next.js Home Search)
     */
    public function search(Request $request)
    {
        $settings = SystemSetting::first();

        // ১. তারিখ ক্যালকুলেশন লজিক
        $advanceDays = ($settings && $settings->advance_booking > 0) ? (int) $settings->advance_booking : 7;
        $pastDays = ($settings && $settings->previous_date_view_allow > 0) ? (int) $settings->previous_date_view_allow : 0;

        $maxDate = Carbon::now()->addDays($advanceDays)->toDateString();
        $minDate = Carbon::now()->subDays($pastDays)->toDateString();

        $requestedDate = $request->date ?? Carbon::now()->toDateString();
        $date = ($requestedDate > $maxDate) ? $maxDate : (($requestedDate < $minDate) ? $minDate : $requestedDate);

        // ২. স্টেশন আইডি বের করা
        $fromID = Station::where('name', $request->from_station)->value('id');
        $toID = Station::where('name', $request->to_station)->value('id');

        $query = Schedule::with(['bus', 'route.routeStationSequences', 'routeFares'])
            ->whereDate('start_time', $date);


        // ৩. সেগমেন্ট ফিল্টারিং (ইন্টারলকিং লজিক)
        if ($fromID && $toID) {
            $posTo = DB::table('route_station_sequence')->where('station_id', $toID)->value('sequence_order');
            if ($posTo) {
                $query->whereHas('route.routeStationSequences', function ($q) use ($toID) {
                    $q->where('station_id', $toID);
                })->whereHas('route.routeStationSequences', function ($q) use ($fromID, $posTo) {
                    $q->where('station_id', $fromID)->where('sequence_order', '<', $posTo);
                });
            }
        }

        $trips = $query->orderBy('start_time', 'ASC')->get();

        // ৪. ডাটা ট্রান্সফর্ম (নেক্সট জেএস-এর জন্য ক্লিন ডাটা)
        $formattedTrips = $trips->map(function ($trip) use ($fromID, $toID) {
            $startTimeObj = \Carbon\Carbon::parse($trip->start_time);
            $endTimeObj = \Carbon\Carbon::parse($trip->end_time);

            $fareObj = $trip->routeFares->first(function ($f) use ($fromID, $toID) {
                return ($f->origin_station_id == $fromID || $f->from_station_id == $fromID) &&
                    ($f->destination_station_id == $toID || $f->to_station_id == $toID);
            });

            return [
                'id' => $trip->id,
                'master_schedule_id' => $trip->master_schedule_id, // অফার মিলানোর জন্য জরুরি
                'trip_code' => $trip->masterSchedule->trip_code ?? 'N/A', // trip_code যোগ করা হলো
                'trip_name' => $trip->name ?? 'N/A',
                'route_name' => $trip->route->name ?? 'N/A',
                'route_id' => $trip->route_id, // অফার মিলানোর জন্য যোগ করা হলো
                'reg_number' => $trip->bus->registration_number ?? '--',
                'bus_type' => $trip->bus_type ?? 'Non AC',
                'departure_time' => $startTimeObj->format('h:i A'),
                'departure_date' => $startTimeObj->format('d M Y'),
                'arrival_time' => $endTimeObj->format('h:i A'),
                'arrival_date' => $endTimeObj->format('d M Y'),
                'fare' => (float) ($fareObj ? $fareObj->price : ($trip->route->fare ?? 0)),
                'available_seats' => $this->calculateAvailableSeats($trip),
                'start_station_id' => $fromID,
                'end_station_id' => $toID,
            ];
        });
        return response()->json([
            'status' => 'success',
            'data' => $formattedTrips,
        ]);
    }

    /**
     * সিট লেআউট এবং স্ট্যাটাস (ইন্টারলকিং ওভারল্যাপ সহ)
     */
    public function getSeatsResponse($scheduleId, $userFromId, $userToId)
    {
        try {
            $schedule = Schedule::with(['seat_layout', 'route.routeStationSequences'])->findOrFail($scheduleId);
            $journeyDate = Carbon::parse($schedule->start_time)->toDateString();

            // রুট সিকোয়েন্স ম্যাপ
            $routeSequences = $schedule->route->routeStationSequences->pluck('sequence_order', 'station_id')->toArray();
            $userFromSeq = $routeSequences[$userFromId] ?? 0;
            $userToSeq = $routeSequences[$userToId] ?? 999;

            // ঐ তারিখের বুকড টিকেট
            $occupiedTickets = TicketIssue::where('schedule_id', $scheduleId)
                ->whereDate('journey_date', $journeyDate)
                ->whereIn('status_label', ['Sold', 'Booked'])
                ->get();

            // লক করা সিট
            $activeLocks = SeatLock::where('schedule_id', $scheduleId)->where('expires_at', '>', now())->pluck('seat_number')->toArray();

            $layout = $schedule->seat_layout;
            $seats = [];
            for ($r = 1; $r <= ($layout->rows ?? 10); $r++) {
                for ($c = 1; $c <= ($layout->columns ?? 4); $c++) {
                    $seatNo = chr(64 + $r) . $c;
                    $status = 'available';

                    // ওভারল্যাপ চেক
                    foreach ($occupiedTickets as $ticket) {
                        $bookedSeats = explode(',', $ticket->seat_numbers);
                        if (in_array($seatNo, $bookedSeats)) {
                            $tFrom = $routeSequences[$ticket->from_station_id] ?? 0;
                            $tTo = $routeSequences[$ticket->to_station_id] ?? 999;

                            // মেইন লজিক: ইন্টারলকিং ওভারল্যাপ
                            if ($tFrom < $userToSeq && $tTo > $userFromSeq) {
                                $status = (strtolower($ticket->status_label) == 'sold') ? 'sold' : 'booked';
                                break;
                            }
                        }
                    }

                    if ($status == 'available' && in_array($seatNo, $activeLocks)) {
                        $status = 'engaged';
                    }

                    $seats[] = ['seat_number' => $seatNo, 'status' => $status];
                }
            }

            return response()->json(['status' => true, 'seats' => $seats]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function calculateAvailableSeats($trip)
    {
        // সহজ ক্যালকুলেশন: মোট সিট - বুকড সিট
        $total = $trip->seat_layout->total_seats ?? 40;
        $booked = TicketIssue::where('schedule_id', $trip->id)
            ->whereIn('status_label', ['Sold', 'Booked'])
            ->sum('seats_count');
        return $total - $booked;
    }
}