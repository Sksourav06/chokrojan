<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ZoneController extends Controller
{
    private $availableStatuses = ['active', 'inactive'];

    /**
     * Display a listing of the zones.
     */
    public function index()
    {
        $user = Auth::user();
        $zones = Zone::all();
        return view('admin.zones.index', compact('zones', 'user'));
    }

    /**
     * Show the form for creating a new zone.
     */
    public function create()
    {
        $user = Auth::user();
        $availableStatuses = $this->availableStatuses;
        return view('admin.zones.create', compact('availableStatuses', 'user'));
    }

    /**
     * Store a newly created zone in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:zones,name',
            'status' => ['required', Rule::in($this->availableStatuses)],
        ]);

        Zone::create($request->all());

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone created successfully!');
    }

    /**
     * Show the form for editing the specified zone.
     */
    public function edit(Zone $zone)
    {
        $availableStatuses = $this->availableStatuses;
        return view('admin.zones.edit', compact('zone', 'availableStatuses'));
    }

    /**
     * Update the specified zone in storage.
     */
    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            // Ignore current zone ID for unique check
            'name' => 'required|string|max:255|unique:zones,name,' . $zone->id,
            'status' => ['required', Rule::in($this->availableStatuses)],
        ]);

        $zone->update($request->only(['name', 'status']));

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone ' . $zone->name . ' updated successfully!');
    }

    /**
     * Remove the specified zone from storage.
     */
    public function destroy(Zone $zone)
    {
        // Add checks here if the zone is linked to active routes

        $zone->delete();
        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone ' . $zone->name . ' deleted successfully.');
    }
}