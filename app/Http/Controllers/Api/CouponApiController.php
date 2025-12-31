<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CouponApiController extends Controller
{
    public function validateCoupon(Request $request)
    {
        $request->validate(['code' => 'required', 'fare' => 'required|numeric']);

        $today = Carbon::today()->toDateString();
        $coupon = Coupon::where('coupon_code', $request->code)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();

        if (!$coupon) {
            return response()->json(['status' => false, 'message' => 'Invalid or Expired Coupon!'], 404);
        }

        if ($request->fare < $coupon->min_fare) {
            return response()->json([
                'status' => false,
                'message' => 'Minimum fare required ৳' . $coupon->min_fare
            ], 400);
        }

        return response()->json([
            'status' => true,
            'discount' => $coupon->discount_amount,
            'message' => 'Coupon Applied Successfully!',
        ]);
    }
}