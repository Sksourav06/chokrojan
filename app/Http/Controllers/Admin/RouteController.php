<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RouteStationSequence;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Zone;
use App\Models\Station;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;

class RouteController extends Controller
{
    private $availableStatuses = ['active', 'inactive'];

    /**
     * Display a listing of the routes.
     */
    public function index()
    {
        $user = Auth::user();
        // Eager load related Zone and Stations
        $routes = Route::with(['zone', 'stations'])->get();
        return view('admin.routes.index', compact('routes', 'user'));
    }

    /**
     * Show the form for creating a new route.
     */
    public function create()
    {
        $user = Auth::user();

        // zones এর জন্য pluck() ঠিক আছে, কারণ এটি অ্যাসোসিয়েটিভ অ্যারে চায়
        $zones = Zone::pluck('name', 'id');

        // 🚨 ফিক্স: View-তে লুপ করার জন্য এবং $station->id অ্যাক্সেস করার জন্য get() ব্যবহার করুন।
        $allStations = Station::where('status', 'active')->orderBy('name')->get();

        // পুরনো pluck ব্যবহার করার উদাহরণ: 
        // $allStations = Station::where('status', 'active')->orderBy('name')->pluck('name', 'id'); 
        // এই ক্ষেত্রে ভিউতে লুপটি পরিবর্তন করা লাগতো।


        $availableStatuses = $this->availableStatuses;

        return view('admin.routes.create', compact('zones', 'allStations', 'availableStatuses', 'user'));
    }

    public function getStations($routeId)
    {
        try {
            $route = Route::with('stations')->findOrFail($routeId);
            return response()->json([
                'success' => true,
                'stations' => $route->stations->map(function ($station) {
                    return [
                        'id' => $station->id,
                        'name' => $station->name,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            \Log::error('Station Load Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading stations'], 500);
        }
    }


    /**
     * Store a newly created route in storage.
     */
    public function store(Request $request)
    {
        // Clone the required_times array for validation purposes, excluding the first element (Start Station time)
        $timesToValidate = collect($request->required_times)->slice(1)->all();

        // 1. Validation (Using the sliced array size for count check)
        $request->validate([
            'name' => 'required|string|max:255|unique:routes,name',
            'zone_id' => 'required|exists:zones,id',
            'status' => ['required', Rule::in($this->availableStatuses)],

            'station_ids' => 'required|array|min:2',
            'station_ids.*' => 'exists:stations,id',

            // Ensure the count of station IDs matches the overall length of required_times
            // We validate the remaining elements strictly
            'required_times' => 'required|array|size:' . count($request->station_ids),
            'required_times.0' => 'nullable', // Allow the 0 index (Start Time) to be non-validated
            'required_times.*' => 'nullable|regex:/^([0-9]{1,2}:[0-5][0-9])$/', // Strict HH:MM validation for others
        ]);

        DB::transaction(function () use ($request) {
            // Create the main Route record
            $route = Route::create($request->only(['name', 'zone_id', 'status']));

            $stationData = [];
            $stationIds = $request->station_ids;
            $requiredTimes = $request->required_times;

            // 2. Build Pivot Data with Sequence and Required Time
            foreach ($stationIds as $index => $stationId) {
                // Index 0 (Start Station) is hardcoded as '00:00' for database consistency
                if ($index === 0) {
                    $timeValue = '00:00';
                } else {
                    $timeValue = $requiredTimes[$index];
                }

                // Attach Station ID as key, and Pivot Data as value (including required_time)
                $stationData[$stationId] = [
                    'sequence_order' => $index + 1, // 1, 2, 3...
                    'required_time' => $timeValue,  // Save the calculated time
                ];
            }

            // Attach all pivot data to the route
            $route->stations()->sync($stationData);
        });

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route created successfully with sequential stations and times!');
    }
    public function edit(Route $route)
    {
        $user = Auth::user();
        // Load related data for dropdowns and current route status
        $zones = Zone::pluck('name', 'id');
        $allStations = Station::where('status', 'active')->orderBy('name')->get();
        $availableStatuses = $this->availableStatuses;

        // Ensure stations are loaded in the correct pivot order
        $route->load([
            'stations' => function ($query) {
                $query->orderBy('sequence_order');
            }
        ]);

        return view('admin.routes.edit', compact('route', 'zones', 'allStations', 'availableStatuses', 'user'));
    }

    /**
     * Update the specified route in storage.
     */
    public function update(Request $request, Route $route)
    {
        // 1. Setup Base Validation Rules
        $validationRules = [
            'name' => 'required|string|max:255|unique:routes,name,' . $route->id,
            'zone_id' => 'required|exists:zones,id',
            'status' => ['required', Rule::in($this->availableStatuses)],

            'station_ids' => 'required|array|min:2',
            'station_ids.*' => 'exists:stations,id',

            'required_times' => 'required|array|size:' . count($request->station_ids),

            // ⭐ FIX 1: Make index 0 required, but we will rely on the database insert logic 
            // to set it to 00:00 correctly. We prevent *.* rule from being applied to it later.
            'required_times.0' => 'required',
        ];

        // ⭐ FIX 2: Apply the strict HH:MM regex only to all indices AFTER index 0. ⭐
        for ($i = 1; $i < count($request->station_ids); $i++) {
            $validationRules['required_times.' . $i] = 'required|regex:/^([0-9]{1,2}:[0-5][0-9])$/';
        }

        $request->validate($validationRules);

        DB::transaction(function () use ($request, $route) {

            $route->update($request->only(['name', 'zone_id', 'status']));

            $stationData = [];
            $stationIds = $request->station_ids;
            $requiredTimes = $request->required_times;

            // 2. Build Pivot Data with Sequence and Required Time
            foreach ($stationIds as $index => $stationId) {

                // Start Station's Required Time is always '00:00' regardless of what the readonly field sends
                $timeValue = ($index === 0) ? '00:00' : $requiredTimes[$index];

                // Attach Station ID as key, and Pivot Data as value
                $stationData[$stationId] = [
                    'sequence_order' => $index + 1,
                    'required_time' => $timeValue,
                ];
            }

            // Sync (update/replace) the pivot table relationship
            $route->stations()->sync($stationData);
        });

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route ' . $route->name . ' updated successfully!');
    }

    public function showStations($routeId)
    {
        try {
            $stations = DB::table('route_station_sequence')
                ->join('stations', 'stations.id', '=', 'route_station_sequence.station_id')
                ->where('route_station_sequence.route_id', $routeId)
                ->orderBy('route_station_sequence.sequence_order', 'asc')
                ->select('stations.id', 'stations.name', 'route_station_sequence.sequence_order')
                ->get();

            $html = view('admin.routes.partials.stations-view', compact('stations'))->render();

            return response()->json(['status' => true, 'html' => $html]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error loading route stations',
                'error' => $e->getMessage(), // 👈 এখানেই সঠিক Error দেখা যাবে
            ], 500);
        }
    }


    public function stationsView($routeId)
    {
        try {
            $stations = DB::table('route_station_sequence as rss')
                ->join('stations as s', 'rss.station_id', '=', 's.id')
                ->where('rss.route_id', $routeId)
                ->orderBy('rss.sequence_order', 'asc')
                ->select('s.id', 's.name', 'rss.sequence_order')
                ->get();

            $stations = collect($stations)->map(function ($station) {
                $station = (object) $station;
                $station->counters = DB::table('counters')
                    ->where('station_id', $station->id)
                    ->select('id', 'name', 'from_time', 'to_time')
                    ->get();
                return $station;
            });

            // 🔍 Debug check
            // dd($stations);

            $html = view('admin.routes.partials.stations-view', compact('stations'))->render();

            return response()->json([
                'status' => true,
                'html' => $html,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error loading route stations',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function getRouteStations($routeId)
    {
        $routeStations = RouteStationSequence::with('station')
            ->where('route_id', $routeId)
            ->orderBy('sequence_order')
            ->get();

        return response()->json($routeStations);
    }

}

// ... (edit, update, destroy methods would follow here)
