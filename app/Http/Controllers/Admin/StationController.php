<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use Illuminate\Http\Request;
use App\Models\Station;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StationController extends Controller
{
    private $availableStatuses = ['active', 'inactive'];

    /**
     * Display a listing of the stations.
     */
    public function index()
    {
        $stations = Station::all();
        $user = Auth::user();
        return view('admin.stations.index', compact('stations', 'user'));
    }

    /**
     * Show the form for creating a new station.
     */
    public function create()
    {
        $user = Auth::user();
        $availableStatuses = $this->availableStatuses;
        return view('admin.stations.create', compact('availableStatuses', 'user'));
    }

    /**
     * Store a newly created station in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stations,name',
            'status' => ['required', Rule::in($this->availableStatuses)],
        ]);

        Station::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.stations.index')
            ->with('success', 'Station created successfully!');
    }

    // Edit method is required by Route::resource
    public function edit(Station $station)
    {
        $user = Auth::user();
        $availableStatuses = $this->availableStatuses;
        return view('admin.stations.edit', compact('station', 'availableStatuses', 'user'));
    }

    // Update method is required by Route::resource
    public function update(Request $request, Station $station)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:stations,name,' . $station->id,
            'status' => ['required', Rule::in($this->availableStatuses)],
        ]);

        $station->update($request->only(['name', 'status']));

        return redirect()->route('admin.stations.index')
            ->with('success', 'Station updated successfully!');
    }

    // Destroy method is required by Route::resource
    public function destroy(Station $station)
    {
        // Add checks here if the station is linked to active routes/trips

        $station->delete();
        return redirect()->route('admin.stations.index')
            ->with('success', 'Station ' . $station->name . ' deleted successfully.');
    }

    public function getCounters($stationId)
    {
        try {
            $counters = Counter::where('station_id', $stationId)
                ->select('id', 'name')
                ->get();

            return response()->json([
                'status' => true,
                'counters' => $counters,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to load counters',
                'error' => $e->getMessage(),
            ]);
        }
    }

}