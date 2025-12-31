<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use Carbon\Carbon;

class OfferController extends Controller
{
    public function getActiveOffers()
    {
        try {
            $today = Carbon::today()->toDateString();

            $offers = Offer::where('is_active', true)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->select('offer_name', 'schedule_id', 'route_id', 'bus_type', 'min_fare', 'max_fare', 'discount_amount')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Active offers fetched successfully.',
                'data' => $offers,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}