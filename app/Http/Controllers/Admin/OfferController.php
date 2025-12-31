<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Route;
use App\Models\MasterSchedule; // MasterSchedule ইমপোর্ট নিশ্চিত করুন
use Carbon\Carbon;

class OfferController extends Controller
{
    public function index()
    {
        // masterSchedule ইগার লোড করা হয়েছে যাতে লিস্টে trip_code দেখা যায়
        $offers = Offer::with(['route', 'masterSchedule'])->latest()->get();
        return view('admin.offers.index', compact('offers'));
    }

    public function create()
    {
        $routes = Route::all();
        // ড্রপডাউনের জন্য trip_code সহ মাস্টার শিডিউল আনা হচ্ছে
        $schedules = MasterSchedule::select('id', 'trip_code', 'bus_type')->get();
        return view('admin.offers.create', compact('routes', 'schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'offer_name' => 'required|string|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'min_fare' => 'required|numeric|min:0',
            'max_fare' => 'required|numeric|gte:min_fare',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'schedule_id' => 'nullable|exists:master_schedules,id', // master_schedules টেবিলে আইডি চেক করবে
            'route_id' => 'nullable|exists:routes,id',
            'bus_type' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        // schedule_id হিসেবে মূলত master_schedule এর ID সেভ হবে
        Offer::create($request->all());

        return redirect()->route('admin.offers.index')->with('success', 'Offer Created Successfully with Trip Code!');
    }

    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        $routes = Route::all();
        $schedules = MasterSchedule::select('id', 'trip_code', 'bus_type')->get();

        return view('admin.offers.edit', compact('offer', 'routes', 'schedules'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'offer_name' => 'required|string|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'min_fare' => 'required|numeric|min:0',
            'max_fare' => 'required|numeric|gte:min_fare',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'schedule_id' => 'nullable|exists:master_schedules,id',
            'is_active' => 'required|boolean',
        ]);

        $offer = Offer::findOrFail($id);
        $offer->update($request->all());

        return redirect()->route('admin.offers.index')->with('success', 'Offer Updated Successfully!');
    }
}