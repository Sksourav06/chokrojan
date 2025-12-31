<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Counter;
use App\Models\Station;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Route;
use App\Models\User;
use Illuminate\Support\Facades\DB; // ⭐ DB Transaction for multi-step operations
use Throwable;

class CounterController extends Controller
{
    private $availableTypes = ['Own', 'Commission'];
    private $availableStatuses = ['active', 'inactive'];

    // 🚨 ASSUMPTION: The pivot table for Counter and Route commissions is named 'counter_route'.

    public function index()
    {
        $user = Auth::user();
        $counters = Counter::with('station', 'routes')->paginate(25);
        return view('admin.counters.index', compact('counters', 'user'));
    }

    public function create()
    {
        $user = Auth::user();
        $stations = Station::pluck('name', 'id');
        $availableTypes = $this->availableTypes;
        $availableStatuses = $this->availableStatuses;
        $routes = Route::where('status', 'active')->get();

        return view('admin.counters.create', compact('stations', 'availableTypes', 'availableStatuses', 'user', 'routes'));
    }

    /**
     * Store a newly created counter and its commissions (if applicable).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'name' => 'required|string|max:255',
            'from_time' => 'nullable|string|max:10',
            'to_time' => 'nullable|string|max:10',
            // 🚨 FIX 1: Added validation for crucial 'type' and 'status' fields
            'counter_type' => ['required', 'string', Rule::in($this->availableTypes)],
            'status' => ['required', 'string', Rule::in($this->availableStatuses)],

            // Validation for commission fields (if 'Commission' type is selected)
            'commission_data' => 'nullable|array',
            'commission_data.*.ac_commission' => 'nullable|numeric|min:0',
            'commission_data.*.non_ac_commission' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create the Counter
            $counter = Counter::create([
                'station_id' => $validated['station_id'],
                'name' => $validated['name'],
                'from_time' => $validated['from_time'] ?? null,
                'to_time' => $validated['to_time'] ?? null,
                'counter_type' => $validated['counter_type'], // Saved the type
                'status' => $validated['status'],           // Saved the status
                'is_own_counter' => ($validated['counter_type'] === 'Own'),
            ]);

            // 🚨 FIX 2: Added logic to sync commissions if Counter Type is 'Commission'
            if ($validated['counter_type'] === 'Commission' && !empty($validated['commission_data'])) {
                $commissionData = [];
                foreach ($validated['commission_data'] as $routeId => $commissions) {
                    // Only sync if at least one commission value is provided
                    if (isset($commissions['ac_commission']) || isset($commissions['non_ac_commission'])) {
                        $commissionData[$routeId] = [
                            'ac_commission' => $commissions['ac_commission'] ?? 0,
                            'non_ac_commission' => $commissions['non_ac_commission'] ?? 0,
                        ];
                    }
                }
                // Sync the pivot table data
                $counter->routes()->sync($commissionData);
            }

            DB::commit();

            return redirect()
                ->route('admin.counters.index')
                ->with('success', 'Counter and commissions created successfully!');

        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create counter: ' . $e->getMessage()]);
        }
    }

    public function edit(Counter $counter)
    {
        $user = Auth::user();
        $stations = Station::pluck('name', 'id');
        $availableTypes = $this->availableTypes;
        $availableStatuses = $this->availableStatuses;

        $routes = Route::where('status', 'active')->get();
        // Load commissions only if the counter type is 'Commission'
        $existingCommissions = ($counter->counter_type === 'Commission')
            ? $counter->routes->keyBy('id')->map(function ($route) {
                return [
                    'ac_commission' => $route->pivot->ac_commission,
                    'non_ac_commission' => $route->pivot->non_ac_commission,
                ];
            })
            : collect(); // Return empty collection if not a Commission type

        return view('admin.counters.edit', compact('counter', 'stations', 'availableTypes', 'availableStatuses', 'routes', 'existingCommissions', 'user'));
    }

    public function update(Request $request, $counterId)
    {
        // 1. Find the counter model instance
        $counter = Counter::findOrFail($counterId);

        // 2. Validate all fields, including dynamic type/status validation
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'name' => 'required|string|max:255',
            'from_time' => 'nullable|string|max:10',
            'to_time' => 'nullable|string|max:10',
            'counter_type' => ['required', 'string', Rule::in($this->availableTypes)],
            'status' => ['required', 'string', Rule::in($this->availableStatuses)],
            'commission_data' => 'nullable|array',
            'commission_data.*.ac_commission' => 'nullable|numeric|min:0',
            'commission_data.*.non_ac_commission' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 3. Update the Counter details (safe Eloquent update)
            $counter->update([
                'station_id' => $validated['station_id'],
                'name' => $validated['name'],
                'from_time' => $validated['from_time'] ?? null,
                'to_time' => $validated['to_time'] ?? null,
                'counter_type' => $validated['counter_type'],
                'status' => $validated['status'],
                'is_own_counter' => ($validated['counter_type'] === 'Own'),
            ]);

            // 🚨 FIX 4: Logic to sync/update commissions
            if ($validated['counter_type'] === 'Commission' && !empty($validated['commission_data'])) {
                $commissionData = [];
                foreach ($validated['commission_data'] as $routeId => $commissions) {
                    if (isset($commissions['ac_commission']) || isset($commissions['non_ac_commission'])) {
                        $commissionData[$routeId] = [
                            'ac_commission' => $commissions['ac_commission'] ?? 0,
                            'non_ac_commission' => $commissions['non_ac_commission'] ?? 0,
                        ];
                    }
                }
                $counter->routes()->sync($commissionData);
            } else {
                // If type is switched to 'Own', detach all previous commissions
                $counter->routes()->detach();
            }

            DB::commit();

            return redirect()
                ->route('admin.counters.index')
                ->with('success', 'Counter updated successfully!');

        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update counter: ' . $e->getMessage()]);
        }
    }

    public function destroy(Counter $counter)
    {
        // 🚨 Enhancement: Use DB transaction to ensure commissions are detached first
        DB::beginTransaction();
        try {
            $counter->routes()->detach(); // Detach commissions first
            $counter->delete();
            DB::commit();

            return redirect()->route('admin.counters.index')
                ->with('success', 'Counter ' . $counter->name . ' deleted successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete counter: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX Method: Fetches routes and existing commissions for the creation/edit view.
     */
    public function getRouteCommissions(Request $request)
    {
        $counterId = $request->input('counter_id');
        $counterType = $request->input('counter_type');

        $routes = Route::where('status', 'active')->get();
        $existingCommissions = collect();

        // Load existing commissions only if a counter ID is provided AND the type is 'Commission'
        if ($counterId && $counterType === 'Commission') {
            $counter = Counter::find($counterId);
            if ($counter) {
                // Key the commission data by route ID
                $existingCommissions = $counter->routes->keyBy('id')->map(function ($route) {
                    return [
                        'ac_commission' => $route->pivot->ac_commission,
                        'non_ac_commission' => $route->pivot->non_ac_commission,
                    ];
                });
            }
        }

        // Return the HTML partial view
        return response()->json([
            'html' => view('admin.counters.partials.route_commission_table', compact('routes', 'existingCommissions'))->render(),
        ]);
    }

    public function show(Counter $counter)
    {
        // 🚨 Recommended fix: Redirect the user to the Edit page if they hit the show route
        return redirect()->route('admin.counters.edit', $counter)->with('info', 'Viewing counter details on the edit page.');

        // OR: Return a 404 response if the show route is not intended to be publicly accessible.
        // abort(404);
    }
    public function getCounterUsers($counterId)
    {
        $users = User::where('counter_id', $counterId)->select('id', 'name')->get();
        return response()->json($users);
    }

}