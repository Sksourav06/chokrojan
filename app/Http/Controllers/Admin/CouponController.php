<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * সকল কুপনের তালিকা দেখানো
     */
    public function index()
    {
        $coupons = Coupon::latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * কুপন তৈরির ফর্ম
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * নতুন কুপন ডাটাবেসে সেভ করা
     */
    public function store(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|unique:coupons,coupon_code',
            'offer_name' => 'required|string|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'min_fare' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        Coupon::create($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully!');
    }

    /**
     * কুপন এডিট করার ফর্ম
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * কুপন আপডেট করা
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'coupon_code' => 'required|unique:coupons,coupon_code,' . $coupon->id,
            'offer_name' => 'required|string|max:255',
            'discount_amount' => 'required|numeric|min:0',
            'min_fare' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        $coupon->update($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully!');
    }

    /**
     * কুপন ডিলিট করা
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully!');
    }
}