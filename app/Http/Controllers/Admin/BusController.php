<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\SeatLayout; // SeatLayout Model needed for foreign key relationship
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class BusController extends Controller
{
    /**
     * Display a listing of the buses.
     */
    public function index()
    {
        // Fetch buses and eager load the seat layout details
        $buses = Bus::with('seatLayout')->get();
        $user = Auth::user();
        return view('admin.buses.index', compact('buses', 'user'));
    }

    /**
     * Show the form for creating a new bus.
     */
    public function create()
    {
        $user = Auth::user();
        $availableBusTypes = ['Non AC', 'AC', 'Sleeper'];
        $availableStatuses = ['running', 'maintenance', 'inactive'];
        // Fetch all defined seat layouts to populate the dropdown
        $seatLayouts = SeatLayout::all()->pluck('name', 'id');

        return view('admin.buses.create', compact('availableBusTypes', 'availableStatuses', 'seatLayouts', 'user'));
    }

    /**
     * Store a newly created bus in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:255|unique:buses,registration_number',
            'make_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'model_name' => 'nullable|string|max:255',
            'bus_type' => ['required', Rule::in(['Non AC', 'AC', 'Sleeper'])],
            'seat_layout_id' => 'required|exists:seat_layouts,id', // Ensure layout exists
            'status' => ['required', Rule::in(['running', 'maintenance', 'inactive'])],
        ]);

        Bus::create([
            'registration_number' => $request->registration_number,
            'make_year' => $request->make_year,
            'model_name' => $request->model_name,
            'bus_type' => $request->bus_type,
            'seat_layout_id' => $request->seat_layout_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus registered successfully!');
    }

    /**
     * Remove the specified bus from storage (Delete feature).
     */
    public function destroy(Bus $bus)
    {
        // Add checks here if the bus is currently assigned to active trips

        $bus->delete();
        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus ' . $bus->registration_number . ' deleted successfully.');
    }

    public function edit(Bus $bus)
    {
        $user = Auth::user();
        $availableBusTypes = ['Non AC', 'AC', 'Sleeper'];
        $availableStatuses = ['running', 'maintenance', 'inactive'];
        // Fetch all defined seat layouts for the dropdown
        $seatLayouts = SeatLayout::all()->pluck('name', 'id');

        // Pass the bus data and supporting arrays to the view
        return view('admin.buses.edit', compact('bus', 'availableBusTypes', 'availableStatuses', 'seatLayouts', 'user'));
    }

    /**
     * Update the specified bus in storage.
     */
    public function update(Request $request, Bus $bus)
    {
        $request->validate([
            // Registration number must be unique, ignoring the current bus ID
            'registration_number' => 'required|string|max:255|unique:buses,registration_number,' . $bus->id,
            'make_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 1),
            'model_name' => 'nullable|string|max:255',
            'bus_type' => ['required', Rule::in(['Non AC', 'AC', 'Sleeper'])],
            'seat_layout_id' => 'required|exists:seat_layouts,id',
            'status' => ['required', Rule::in(['running', 'maintenance', 'inactive'])],
        ]);

        $bus->update([
            'registration_number' => $request->registration_number,
            'make_year' => $request->make_year,
            'model_name' => $request->model_name,
            'bus_type' => $request->bus_type,
            'seat_layout_id' => $request->seat_layout_id,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus ' . $bus->registration_number . ' updated successfully!');
    }

    // You would implement edit(Bus $bus) and update(Request $request, Bus $bus) here as well.
}