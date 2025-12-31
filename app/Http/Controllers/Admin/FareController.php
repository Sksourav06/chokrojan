<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fare;
use App\Models\FareStationPrice;
use App\Models\Route;
use App\Models\SeatLayout;
use App\Models\Station;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FareController extends Controller
{
    private $availableStatuses = ['active', 'inactive'];
    // ফর্মের 'vehicle_type_id' (1, 2) কে ডেটাবেসের 'bus_type' (AC, Non AC) এ ম্যাপ করার জন্য
    private $busTypes = ['1' => 'AC', '2' => 'Non AC'];

    public function index()
    {
        $user = Auth::user();
        $fares = Fare::with(['route', 'seatLayout', 'stationPrices.origin', 'stationPrices.destination'])
            ->paginate(25);
        return view('admin.fares.index', compact('fares', 'user'));
    }

    public function create()
    {
        $user = Auth::user();
        $routes = Route::where('status', 'active')->orderBy('name')->get();
        $seatLayouts = SeatLayout::all();
        $availableStatuses = $this->availableStatuses;
        $busTypes = $this->busTypes;

        return view('admin.fares.create', compact('user', 'routes', 'seatLayouts', 'availableStatuses', 'busTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fares,name',
            'route_id' => 'required|exists:routes,id',
            'seat_layout_id' => 'required|exists:seat_layouts,id',
            'vehicle_type_id' => ['required', Rule::in(array_keys($this->busTypes))],
            'status' => ['required', Rule::in($this->availableStatuses)],
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'stsids' => 'required|array|min:1',
            'stsfares' => 'required|array|min:1',
            'stsfares.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $fare = Fare::create([
                'name' => $request->name,
                'route_id' => $request->route_id,
                'seat_layout_id' => $request->seat_layout_id,
                'bus_type' => $this->busTypes[$request->vehicle_type_id],
                'start_date' => $request->from_date,
                'end_date' => $request->to_date,
                'status' => $request->status,
            ]);

            foreach ($request->stsids as $index => $stationPair) {
                list($origin_id, $dest_id) = explode('-', $stationPair);

                FareStationPrice::create([
                    'fare_id' => $fare->id,
                    'route_id' => $fare->route_id, // <--- **ফিক্স:** route_id যোগ করা হলো
                    'origin_station_id' => $origin_id,
                    'destination_station_id' => $dest_id,
                    'price' => $request->stsfares[$index],
                ]);
            }
        });

        return redirect()->route('admin.fares.index')
            ->with('success', 'Fare rule created successfully!');
    }
    /**
     * AJAX method to get station pairs for a route.
     */
    public function getStationPairs($routeId)
    {
        $route = Route::findOrFail($routeId);
        $stations = $route->stations;

        $pairs = [];
        foreach ($stations as $origin) {
            foreach ($stations as $destination) {
                if ($origin->id !== $destination->id) {
                    $pairs[] = [
                        'value' => $origin->id . '-' . $destination->id,
                        'text' => $origin->name . ' ⟹ ' . $destination->name,
                    ];
                }
            }
        }

        return response()->json(['pairs' => $pairs]);
    }



    // public function edit(Fare $fare)
    // {
    //     $user = Auth::user();
    //     $routes = Route::where('status', 'active')->orderBy('name')->get();
    //     $seatLayouts = SeatLayout::all();
    //     $availableStatuses = $this->availableStatuses;
    //     $busTypes = $this->busTypes;

    //     // Fetch all stations for the fare's route to display in the UI
    //     $routeStations = $fare->route->stations ?? collect();

    //     // Fetch existing station prices associated with this fare
    //     $stationPrices = $fare->stationPrices->isEmpty() ? [] : $fare->stationPrices->keyBy(function ($item) {
    //         return $item->origin_station_id . '-' . $item->destination_station_id;
    //     });

    //     // Map bus_type back to vehicle_type_id for the form
    //     $vehicleTypeId = array_search($fare->bus_type, $this->busTypes);
    //     $stationPrices = $fare->stationPrices->keyBy(function ($item) {
    //         return $item->origin_station_id . '-' . $item->destination_station_id;
    //     });
    //     // Pass the stationPrices as a JSON object for JavaScript to use
    //     return view('admin.fares.edit', compact(
    //         'user',
    //         'fare', // The fare model being edited
    //         'routes',
    //         'seatLayouts',
    //         'availableStatuses',
    //         'busTypes',
    //         'routeStations',
    //         'stationPrices',  // Pass the data to the view
    //         'vehicleTypeId',
    //     ));
    // }

    public function edit($fareId)
    {
        $fare = Fare::findOrFail($fareId);

        // Load only active routes (Fix)
        $routes = Route::where('status', 'active')
            ->orderBy('name')
            ->get();

        $seatLayouts = SeatLayout::all();
        $busTypes = $this->busTypes;
        $availableStatuses = ['active', 'inactive'];
        $vehicleTypeId = array_search($fare->bus_type, $this->busTypes);

        $stationPrices = FareStationPrice::where('fare_id', $fareId)
            ->with(['origin', 'destination'])
            ->get();

        return view('admin.fares.edit', compact(
            'fare',
            'routes',
            'seatLayouts',
            'busTypes',
            'availableStatuses',
            'stationPrices',
            'vehicleTypeId',
        ));
    }

    /**
     * Update the specified fare in storage.
     */
    public function update(Request $request, Fare $fare)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fares,name,' . $fare->id, // Unique check ignores current ID
            'route_id' => 'required|exists:routes,id',
            'seat_layout_id' => 'required|exists:seat_layouts,id',
            'vehicle_type_id' => ['required', Rule::in(array_keys($this->busTypes))],
            'status' => ['required', Rule::in($this->availableStatuses)],
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'stsids' => 'required|array|min:1', // Station pairs array
            'stsfares' => 'required|array|min:1', // Fares array
            'stsfares.*' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $fare) {

            // 1. Update the main Fare record
            $fare->update([
                'name' => $request->name,
                'route_id' => $request->route_id,
                'seat_layout_id' => $request->seat_layout_id,
                'bus_type' => $this->busTypes[$request->vehicle_type_id],
                'start_date' => $request->from_date,
                'end_date' => $request->to_date,
                'status' => $request->status,
            ]);

            // 2. Clear existing FareStationPrices (simpler than syncing)
            $fare->stationPrices()->delete();

            // 3. Insert new/updated FareStationPrices
            foreach ($request->stsids as $index => $stationPair) {
                list($origin_id, $dest_id) = explode('-', $stationPair);

                FareStationPrice::create([
                    'fare_id' => $fare->id,
                    'route_id' => $fare->route_id,
                    'origin_station_id' => $origin_id,
                    'destination_station_id' => $dest_id,
                    'price' => $request->stsfares[$index],
                ]);
            }
        });

        return redirect()->route('admin.fares.index')
            ->with('success', 'Fare rule updated successfully!');
    }



}