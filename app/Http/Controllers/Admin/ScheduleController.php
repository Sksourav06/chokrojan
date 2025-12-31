<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounterBlockedSeat;
use App\Models\Platform;
use App\Models\ScheduleCounter; // Corrected usage (was ScheduleCounters in updateCounterPermissions)
use App\Models\ScheduleOnDay;
use App\Models\SchedulePlatformPermission;
use Illuminate\Http\Request;
use App\Models\{
    Counter,
    Station,
    Schedule,
    Route,
    Zone,
    Bus,
    FareStationPrice,
    MasterSchedule,
    SeatLayout,
    RouteStationSequence
};
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    private $availableStatuses = ['active', 'inactive', 'hide'];
    private $busTypes = ['AC', 'Non AC', 'Sleeper'];

    // --- Index, Create, Store, Edit Functions (No changes needed here for functionality) ---

    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Base Query: MasterSchedule মডেল থেকে ডেটা লোড করা হচ্ছে
        $query = MasterSchedule::with([
            'bus',              // Load the 'bus' relationship
            'route',            // Load the 'route' relationship (make sure it's defined)
            'startStation',     // Load the 'startStation' relationship
            'endStation',       // Load the 'endStation' relationship
        ]);

        // 2. Filter by Zone (Route সম্পর্কের মাধ্যমে)
        if ($request->filled('zone')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('zone_id', $request->zone);
            });
        }

        // 3. Filter by Route (MasterSchedule এ route_id সরাসরি আছে)
        if ($request->filled('route')) {
            $query->where('route_id', $request->route);
        }

        // 4. Fetch Results (Paginated)
        $schedules = $query->paginate(25);

        // 5. Fetch Filter Data
        $zones = Zone::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');

        return view('admin.schedules.index', compact('schedules', 'user', 'zones', 'routes'));
    }

    public function store(Request $request)
    {
        $rules = [
            'route_tagline' => 'nullable|string|max:255',
            'coach_number' => 'required|string|max:255|unique:schedules,name',
            'route_id' => 'required|exists:routes,id',
            'start_station_id' => 'required|exists:stations,id',
            'end_station_id' => 'required|exists:stations,id|different:start_station_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'bus_id' => 'nullable|exists:buses,id',
            'seat_layout_id' => 'required|exists:seat_layouts,id',
            'bus_type' => ['required', Rule::in($this->busTypes)],
            'status' => ['required', Rule::in($this->availableStatuses)],
            'start_time_nextday' => 'nullable|boolean',
        ];

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated) {
            // Step 1: Get fare_station_prices data using start_station_id and end_station_id
            $fare = FareStationPrice::where('origin_station_id', $validated['start_station_id'])
                ->where('destination_station_id', $validated['end_station_id'])
                ->first(); // You may need to add logic to handle no result from this query

            // Step 2: Use the fetched data to populate origin_station_id and destination_station_id in master schedule
            $masterSchedule = MasterSchedule::create([
                'trip_code' => 'TS-' . strtoupper(uniqid()), // Generate trip code
                'bus_id' => $validated['bus_id'], // Ensure bus_id is passed correctly
                'route_id' => $validated['route_id'],
                'start_station_id' => $validated['start_station_id'],
                'end_station_id' => $validated['end_station_id'],
                'start_time_only' => Carbon::createFromFormat('H:i', $validated['start_time'])->format('H:i:s'),
                'end_time_only' => Carbon::createFromFormat('H:i', $validated['end_time'])->format('H:i:s'),
                'bus_type' => $validated['bus_type'],
                'status' => $validated['status'],
                'start_time_nextday' => $validated['start_time_nextday'] ?? 0,
                'origin_station_id' => $fare->origin_station_id ?? null, // Set from fare_station_prices table
                'destination_station_id' => $fare->destination_station_id ?? null, // Set from fare_station_prices table
            ]);

            // Step 3: Create the schedule record and attach master_schedule_id
            Schedule::create([
                'name' => $validated['coach_number'],
                'route_tagline' => $validated['route_tagline'] ?? null,
                'route_id' => $validated['route_id'],
                'start_station_id' => $validated['start_station_id'],
                'end_station_id' => $validated['end_station_id'],
                'start_time' => Carbon::createFromFormat('H:i', $validated['start_time'])->format('H:i:s'),
                'end_time' => Carbon::createFromFormat('H:i', $validated['end_time'])->format('H:i:s'),
                'bus_id' => $validated['bus_id'] ?? null,
                'seat_layout_id' => $validated['seat_layout_id'],
                'bus_type' => $validated['bus_type'],
                'status' => $validated['status'],
                'start_time_nextday' => $validated['start_time_nextday'] ?? 0,
                'master_schedule_id' => $masterSchedule->id, // Attach master_schedule_id here
            ]);
        });

        return redirect()->route('admin.schedules.index')
            ->with('success', 'New schedule created successfully!');
    }




    public function edit(Schedule $schedule)
    {
        $user = Auth::user();
        $routes = Route::where('status', 'active')->orderBy('name')->pluck('name', 'id');
        $buses = Bus::pluck('registration_number', 'id');
        $seatLayouts = SeatLayout::pluck('name', 'id');
        $availableStatuses = $this->availableStatuses;
        $busTypes = $this->busTypes;
        $allStations = Station::where('status', 'active')->orderBy('name')->pluck('name', 'id');
        $schedulePlatformList = \App\Models\SchedulePlatformPermission::where('schedule_id', $schedule->id)->get();
        $schedule->load('seatPlan');

        // Seat Layout Logic
        $seatLayout = [];
        if ($schedule->seatPlan && $schedule->seatPlan->seat_map_config) {
            $config = $schedule->seatPlan->seat_map_config;
            if (is_string($config)) {
                $config = json_decode($config, true);
            }
            if (isset($config['pattern']) && is_array($config['pattern'])) {
                $seatLayout = $config['pattern'];
            }
        }

        // ⭐ স্টেশনের সিকুয়েন্স লোড করা
        $routeStationList = RouteStationSequence::with(['station'])
            ->where('route_id', $schedule->route_id)
            ->orderBy('sequence_order')
            ->get();

        // ⭐ আগে থেকে সেভ করা কাউন্টার লিস্ট
        $scheduleCounterList = ScheduleCounter::where('schedule_id', $schedule->id)->get();

        // ⭐ ড্রপডাউনের জন্য সব কাউন্টার লোড করে গ্রুপ করা
        $allCounters = Counter::whereIn('station_id', $routeStationList->pluck('station_id'))->get();
        $groupedCounters = $allCounters->groupBy('station_id');

        // জাভাস্ক্রিপ্টের জন্য ডাটা ফরম্যাট (অ্যারে হিসেবে)
        $routeStationCounters = [];
        foreach ($routeStationList as $seq) {
            $stationCounters = $groupedCounters[$seq->station_id] ?? collect([]);
            $routeStationCounters[$seq->station_id] = $stationCounters->map(function ($counter) {
                return [
                    'id' => $counter->id,
                    'name' => $counter->name,
                    'counter_type' => $counter->counter_type,
                    'time' => $counter->time ?? null,
                ];
            })->values();
        }

        // ⭐ ব্লেড ফাইলের জন্য $stations কালেকশন তৈরি (অবজেক্ট হিসেবে)
        $stations = $routeStationList->map(function ($seq) use ($groupedCounters) {
            $stationData = $seq->station; // এটি Eloquent Model Object
            if ($stationData) {
                // এই স্টেশনের নির্দিষ্ট কাউন্টারগুলো অবজেক্ট হিসেবে সেট করা হচ্ছে
                $stationData->available_counters = $groupedCounters->get($stationData->id) ?? collect([]);
                $stationData->sequence_order = $seq->sequence_order ?? null;
            }
            return $stationData;
        })->filter();

        return view('admin.schedules.edit', compact(
            'user',
            'routes',
            'buses',
            'seatLayouts',
            'availableStatuses',
            'busTypes',
            'schedule',
            'routeStationList',
            'routeStationCounters',
            'scheduleCounterList',
            'allStations',
            'seatLayout',
            'stations',
            'schedulePlatformList',
        ));
    }

    public function update(Request $request, Schedule $schedule)
    {
        // 🚨 ফিক্স ১: Validation Rule পরিবর্তন করে h:i A ফরমেটে আনা হলো (যদি আপনার ব্লেড ফাইল সেই ফরমেট ব্যবহার করে)
        $rules = [
            'route_tagline' => 'nullable|string|max:255',
            'coach_number' => 'required|string|max:255', // Unique validation removed
            'route_id' => 'required|exists:routes,id',
            'start_station_id' => 'required|exists:stations,id',
            'end_station_id' => 'required|exists:stations,id|different:start_station_id',

            // 🚨 ফিক্স: h:i A Validation ব্যবহার করা হয়েছে
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:h:i A',

            'bus_id' => 'nullable|exists:buses,id',
            'seat_layout_id' => 'required|exists:seat_layouts,id',
            'bus_type' => ['required', Rule::in($this->busTypes)],
            'status' => ['required', Rule::in($this->availableStatuses)],
            'start_time_nextday' => 'nullable|in:1',
        ];

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $schedule) {

            // 1. বিদ্যমান তারিখ বের করা 
            $scheduleDate = $schedule->start_time->toDateString();

            // 2. নতুন DATETIME তৈরি করা 
            // Carbon::parse() এখন h:i A ফরমেটটি বুঝতে পারবে
            $newStartTime = Carbon::parse($scheduleDate . ' ' . $validated['start_time']);
            $newEndTime = Carbon::parse($scheduleDate . ' ' . $validated['end_time']);

            // 3. মধ্যরাতের লজিক প্রয়োগ 
            $isNextDay = isset($validated['start_time_nextday']);
            if ($isNextDay || $newEndTime->lessThan($newStartTime)) {
                $newEndTime->addDay();
            }

            // 4. Master Schedule আপডেট করা (CRITICAL STEP)
            $masterSchedule = MasterSchedule::find($schedule->master_schedule_id);

            if ($masterSchedule) {
                // Master Schedule এ শুধুমাত্র রুটিন ও সময় অংশ সেভ করা
                $masterSchedule->update([
                    'trip_code' => $validated['coach_number'],
                    'bus_id' => $validated['bus_id'] ?? $masterSchedule->bus_id,
                    'route_id' => $validated['route_id'],
                    'start_station_id' => $validated['start_station_id'],
                    'end_station_id' => $validated['end_station_id'],
                    'bus_type' => $validated['bus_type'],
                    'status' => $validated['status'],
                    // 🚨 Master Schedule এ শুধুমাত্র সময় অংশ সেভ করা হলো
                    'start_time_only' => $newStartTime->format('H:i:s'),
                    'end_time_only' => $newEndTime->format('H:i:s'),
                    'start_time_nextday' => $isNextDay,
                ]);

                // 5. ভবিষ্যতে সকল Daily Schedules আপডেট করা (Time Propagation)
                $updateData = [
                    'name' => $validated['coach_number'],
                    'route_id' => $validated['route_id'],
                    'start_station_id' => $validated['start_station_id'],
                    'end_station_id' => $validated['end_station_id'],
                    'bus_id' => $validated['bus_id'] ?? $masterSchedule->bus_id,
                    'bus_type' => $validated['bus_type'],
                    'status' => $validated['status'],
                    'route_tagline' => $validated['route_tagline'] ?? null,
                    'start_time_nextday' => $isNextDay,

                    // Time propagation using raw SQL CONCAT 
                    // এটি ভবিষ্যতের ট্রিপের তারিখ ঠিক রেখে শুধুমাত্র সময় অংশ পরিবর্তন করে দেবে
                    'start_time' => DB::raw("CONCAT(DATE(start_time), ' " . $newStartTime->format('H:i:s') . "')"),
                    'end_time' => DB::raw("CONCAT(DATE(end_time), ' " . $newEndTime->format('H:i:s') . "')"),
                ];

                // আজকের পরের সকল শিডিউল আপডেট করা
                Schedule::where('master_schedule_id', $masterSchedule->id)
                    ->whereDate('start_time', '>', Carbon::today())
                    ->update($updateData);
            }

            // 6. বর্তমান শিডিউল আপডেট করা (Update current daily schedule)
            // বর্তমান দিনের শিডিউলটি আপডেট করা হচ্ছে 
            $schedule->update([
                'name' => $validated['coach_number'],
                'route_tagline' => $validated['route_tagline'] ?? null,
                'route_id' => $validated['route_id'],
                'start_station_id' => $validated['start_station_id'],
                'end_station_id' => $validated['end_station_id'],

                'start_time' => $newStartTime, // 🚨 Carbon অবজেক্ট সেভ করা হলো
                'end_time' => $newEndTime,

                'bus_id' => $validated['bus_id'] ?? null,
                'seat_layout_id' => $validated['seat_layout_id'],
                'bus_type' => $validated['bus_type'],
                'status' => $validated['status'],
                'start_time_nextday' => $isNextDay,
            ]);
        });

        return redirect()->route('admin.schedules.index')
            ->with('success', "Schedule {$schedule->name} updated successfully!");
    }


    // =========================================================
    // 🚌 TAB 2: Station & Counter Update (AJAX)
    // =========================================================
    public function updateCounters(Request $request, $id)
    {
        try {
            $counterData = $request->input('counter_data', []);

            // Delete old
            ScheduleCounter::where('schedule_id', $id)->delete();

            foreach ($counterData as $data) {
                if (empty($data['counter_id']) || empty($data['station_id'])) {
                    continue;
                }

                ScheduleCounter::create([
                    'schedule_id' => $id,
                    'station_id' => $data['station_id'],
                    'counter_id' => $data['counter_id'],
                    'time' => $data['time'] ?? null,
                    'from_date' => !empty($data['from_date'])
                        ? Carbon::parse($data['from_date'])->toDateString()
                        : Carbon::now()->toDateString(),
                    'to_date' => !empty($data['to_date'])
                        ? Carbon::parse($data['to_date'])->toDateString()
                        : '2099-12-31',
                ]);
            }

            // ✅ redirect to edit page with success message
            return redirect()
                ->route('admin.schedules.edit', $id)
                ->with('success', 'Counters updated successfully.');

        } catch (\Throwable $e) {
            Log::error('updateCounters failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update counters.');
        }
    }

    // =========================================================
    // 🛡️ TAB 3: Counter Permissions (AJAX)
    // =========================================================

    public function getCounterPermissions($scheduleId)
    {
        $schedule = Schedule::with([
            'route.routeStationSequences.station',
            'bus.seatLayout',
        ])->findOrFail($scheduleId);

        $savedCounters = \App\Models\ScheduleCounter::where('schedule_id', $scheduleId)->get();

        $stationIds = $schedule->route->routeStationSequences->pluck('station_id');

        $allRouteCounters = \App\Models\Counter::whereIn('station_id', $stationIds)
            ->get(['id', 'name', 'station_id', 'counter_type']);

        $groupedRouteCounters = $allRouteCounters->groupBy('station_id');

        /** Load blocked seats for ALL counters of this schedule */
        $blockedSeats = \App\Models\CounterBlockedSeat::where('schedule_id', $scheduleId)
            ->pluck('blocked_seats', 'counter_id');

        /** Prepare station → counters list */
        $stationData = $schedule->route->routeStationSequences->map(function ($sequence) use ($savedCounters, $groupedRouteCounters, $blockedSeats) {
            $station = $sequence->station;

            if (!$station)
                return null;

            $routeStationCounters = $groupedRouteCounters->get($station->id) ?? collect([]);

            return [
                'station_id' => $station->id,
                'station_name' => $station->name,
                'counters' => $routeStationCounters->map(function ($counter) use ($savedCounters, $blockedSeats) {
                    $saved = $savedCounters->where('counter_id', $counter->id)->first();

                    return [
                        'id' => $counter->id,
                        'name' => $counter->name,
                        'counter_type' => $counter->counter_type,
                        'from_date' => $saved->from_date ?? null,
                        'to_date' => $saved->to_date ?? null,
                        'blocked_seats' => $blockedSeats[$counter->id] ?? []  // 🔥 HERE
                    ];
                }),
            ];
        })->filter()->values();


        return response()->json([
            'status' => 'success',
            'stations' => $stationData,
            'seat_layout' => $schedule->bus->seatLayout,   // 🔥 seat layout added
        ]);
    }
    public function updateCounterPermissions(Request $request, $scheduleId)
    {
        try {
            DB::beginTransaction();

            // Validate schedule
            $schedule = Schedule::findOrFail($scheduleId);

            // Get data from request
            $data = $request->input('data', []);

            // Remove old permissions
            ScheduleCounter::where('schedule_id', $scheduleId)->delete();

            $inserts = [];

            foreach ($data as $station) {

                // Safety check
                if (!isset($station['station_id']) || !isset($station['counters'])) {
                    continue;
                }

                foreach ($station['counters'] as $counter) {

                    // Safety check: counter_id missing হলে skip
                    if (!isset($counter['counter_id'])) {
                        continue;
                    }

                    // Only save assigned counters
                    if (!isset($counter['assigned']) || $counter['assigned'] !== "true") {
                        continue;
                    }

                    $inserts[] = [
                        'schedule_id' => $scheduleId,
                        'station_id' => $station['station_id'],
                        'counter_id' => $counter['counter_id'],   // <-- Correct key
                        'from_date' => $counter['from_date'] ?? null,
                        'to_date' => $counter['to_date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($inserts)) {
                ScheduleCounter::insert($inserts);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Counter permissions updated successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("updateCounterPermissions error: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }





    public function getSeatLayout($counterId)
    {
        // NOTE: This logic assumes Schedule::first() finds the schedule, which is risky.
        // It's better to pass scheduleId from JS for a real application.
        $schedule = Schedule::with('seatPlan')->first();

        if (!$schedule) {
            return response()->json(['status' => false, 'message' => 'Schedule not found or seat layout missing.'], 404);
        }

        $seatLayout = $schedule->seatPlan;
        $counterBlockedRecord = CounterBlockedSeat::where('schedule_id', $schedule->id)
            ->where('counter_id', $counterId)
            ->first();

        $globalBlockedSeats = is_string($schedule->blocked_seats) ? json_decode($schedule->blocked_seats, true) : ($schedule->blocked_seats ?? []);

        return response()->json([
            'status' => true,
            'message' => 'Seat layout fetched successfully',
            'counter_id' => $counterId,
            'layout' => $seatLayout,
            'blocked_seats' => $globalBlockedSeats,
            'counter_blocked_seats' => $counterBlockedRecord->blocked_seats ?? [],
        ]);
    }

    public function saveBlockedSeats(Request $request)
    {
        $data = $request->validate([
            'schedule_id' => 'required|integer|exists:schedules,id',
            'counter_id' => 'required|integer|exists:counters,id',
            'blocked_seats' => 'nullable|array',
        ]);

        $record = CounterBlockedSeat::updateOrCreate(
            [
                'schedule_id' => $data['schedule_id'],
                'counter_id' => $data['counter_id'],
            ],
            [
                'blocked_seats' => $data['blocked_seats'] ?? [],
            ],
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Blocked seat permissions updated successfully.',
            'blocked_seats' => $record->blocked_seats,
        ]);
    }

    // =========================================================
    // 🪑 TAB 4: Seat Layout & Blocked Seats (AJAX)
    // =========================================================

    public function getRouteStations($routeId)
    {
        $stations = RouteStationSequence::with('station')
            ->where('route_id', $routeId)
            ->orderBy('sequence_order')
            ->get()
            ->map(function ($seq) {
                return $seq->station;
            });

        return response()->json([
            'status' => 'success',
            'stations' => $stations,
        ]);
    }

    public function getSeatMapPreview($layoutId, $busType)
    {
        $seatLayout = SeatLayout::find($layoutId);

        if (!$seatLayout) {
            return response()->json(['status' => false, 'message' => 'Seat layout not found.'], 404);
        }

        $config = $seatLayout->seat_map_config;
        if (is_string($config)) {
            $config = json_decode($config, true);
        }

        $pattern = $config['pattern'] ?? [];

        return response()->json([
            'status' => true,
            'message' => 'Seat map preview fetched successfully',
            'layout' => $pattern,
            'bus_type' => $busType,
        ]);
    }

    public function getBlockedSeats(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer|exists:schedules,id',
            'counter_id' => 'required|integer|exists:counters,id',
        ]);

        $record = CounterBlockedSeat::where('schedule_id', $request->schedule_id)
            ->where('counter_id', $request->counter_id)
            ->first();

        return response()->json([
            'status' => 'success',
            'blocked_seats' => $record ? $record->blocked_seats : [],
        ]);
    }

    public function getPlatformPermissions($scheduleId)
    {
        $platforms = Platform::all();

        $saved = SchedulePlatformPermission::where('schedule_id', $scheduleId)
            ->get()
            ->keyBy('platform_id');

        $data = $platforms->map(function ($p) use ($saved) {
            $row = $saved->get($p->id);

            return [
                'platform_id' => $p->id,
                'name' => $p->name,
                'logo' => $p->logo,
                'from_date' => $row->from_date ?? '',
                'to_date' => $row->to_date ?? '',
                'blocked_seats' => $row->blocked_seats ?? [],
                'status' => $row->status ?? 0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'platforms' => $data,
        ]);
    }

    public function savePlatformPermissions(Request $request, $id)
    {
        try {
            $platforms = $request->input('platforms', []);

            \App\Models\SchedulePlatformPermission::where('schedule_id', $id)->delete();

            foreach ($platforms as $item) {
                \App\Models\SchedulePlatformPermission::create([
                    'schedule_id' => $id,
                    'platform_id' => $item['platform_id'], // numeric id
                    'from_date' => $item['from_date'] ?? now()->toDateString(),
                    'to_date' => $item['to_date'] ?? '2099-12-31',
                    'status' => $item['status'] ?? 0,
                    'blocked_seats' => $item['blocked_seats'] ?? [],
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Platform permissions saved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadCalendar($scheduleId)
    {
        $items = ScheduleOnDay::where('schedule_id', $scheduleId)->get();

        $html = view('admin.schedules.partials.calendar', compact('items'))->render();

        return response()->json(['html' => $html]);
    }

    public function loadOnOffList($scheduleId)
    {
        $items = ScheduleOnDay::where('schedule_id', $scheduleId)
            ->orderBy('from_date', 'asc')
            ->get();

        $html = view('admin.schedules.partials.onoff_list', compact('items'))->render();

        return response()->json(['html' => $html]);
    }
    public function saveOnDays(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'start_time' => 'nullable',
            'weekdays' => 'array',
        ]);

        ScheduleOnDay::create([
            'schedule_id' => $request->schedule_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'start_time' => $request->start_time,
            'weekdays' => $request->weekdays,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Saved successfully",
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        // Master Schedule তৈরি করার জন্য প্রয়োজনীয় সকল ডেটা লোড
        $routes = Route::where('status', 'active')->orderBy('name')->pluck('name', 'id');
        $buses = Bus::pluck('registration_number', 'id');
        $seatLayouts = SeatLayout::pluck('name', 'id');

        // Station গুলোকে অবজেক্ট কালেকশন হিসেবে আনা হলো, যা ব্লেড ফাইলে $station->id অ্যাক্সেস করতে সাহায্য করবে
        $allStations = Station::where('status', 'active')->orderBy('name')->get();

        $availableStatuses = $this->availableStatuses;
        $busTypes = $this->busTypes;

        // Create or find the MasterSchedule and get its ID
        // $masterSchedule = MasterSchedule::create([
        //     'trip_code' => 'your_trip_code', // Set the trip_code or other necessary fields
        //     // 'bus_id' => 'your_bus_id', // Assign bus id
        //     'route_id' => 'your_route_id', // Assign route id
        //     // Add other necessary fields for MasterSchedule
        // ]);

        // When creating a schedule, assign master_schedule_id to it
        return view('admin.schedules.create', compact(
            'user',
            'routes',
            'buses',
            'seatLayouts',
            'availableStatuses',
            'allStations',
            'busTypes',
            // 'masterSchedule' // Pass the MasterSchedule object to view
        ));
    }


}
