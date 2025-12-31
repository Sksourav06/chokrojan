<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyDiscount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
class LoyaltyDiscountController extends Controller
{
    public function index()
    {
        $discounts = LoyaltyDiscount::orderBy('discount_amount', 'desc')->get();

        return view('admin.loyalty.index', compact('discounts'));
    }
    public function create()
    {
        return view('admin.loyalty.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'days_threshold' => 'required|integer|min:1|unique:loyalty_discounts,days_threshold',
            'discount_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        LoyaltyDiscount::create([
            'days_threshold' => $request->days_threshold,
            'discount_amount' => $request->discount_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? $request->is_active : 0,
        ]);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'Discount rule created successfully.');
    }


    public function edit($id)
    {
        $discount = LoyaltyDiscount::findOrFail($id);
        // $discount variable is passed correctly here.
        return view('admin.loyalty.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // আপডেটের সময় বর্তমান ID বাদে অদ্বিতীয়তা চেক করা
            'days_threshold' => ['required', 'integer', 'min:1', Rule::unique('loyalty_discounts')->ignore($id, 'id')],
            'discount_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $discount = LoyaltyDiscount::findOrFail($id);
        $discount->update([
            'days_threshold' => $request->days_threshold,
            'discount_amount' => $request->discount_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? $request->is_active : 0
        ]);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'Discount rule updated successfully.');
    }

    public function destroy($id)
    {
        LoyaltyDiscount::findOrFail($id)->delete();

        // FIX: ভুল রাউট নেম পরিবর্তন করে admin.loyalty.index ব্যবহার করা হলো
        return redirect()->route('admin.loyalty.index')
            ->with('success', 'Discount rule deleted.');
    }
}