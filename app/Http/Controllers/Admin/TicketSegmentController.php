<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketSegment;
use Illuminate\Http\Request;
// use DB;
use Illuminate\Support\Facades\DB;

class TicketSegmentController extends Controller
{
    public function sellSegment(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer',
            'seat_no' => 'required|string',
            'origin_station_id' => 'required|integer',
            'destination_station_id' => 'required|integer',
            'ticket_id' => 'nullable|integer',
        ]);

        return DB::transaction(function () use ($request) {
            $segment = TicketSegment::where([
                'schedule_id' => $request->schedule_id,
                'seat_no' => $request->seat_no,
                'origin_station_id' => $request->origin_station_id,
                'destination_station_id' => $request->destination_station_id,
            ])->lockForUpdate()->first();

            if (!$segment) {
                return response()->json(['success' => false, 'message' => 'Segment not found'], 404);
            }

            if ($segment->status === 'sold') {
                return response()->json(['success' => false, 'message' => 'Already sold'], 409);
            }

            $segment->update([
                'status' => 'sold',
                'ticket_id' => $request->ticket_id ?? $segment->ticket_id
            ]);

            return response()->json(['success' => true, 'message' => 'Segment sold']);
        });
    }

    public function checkAvailability(Request $request)
    {
        $exists = TicketSegment::where([
            'schedule_id' => $request->schedule_id,
            'seat_no' => $request->seat_no,
            'origin_station_id' => $request->origin_station_id,
            'destination_station_id' => $request->destination_station_id,
            'status' => 'sold',
        ])->exists();

        return response()->json(['available' => !$exists]);
    }
}
