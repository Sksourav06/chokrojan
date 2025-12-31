<?php

namespace App\Helpers;

use App\Models\Ticket;
use App\Models\LoyaltyDiscount;
use App\Models\TicketIssue;
use Carbon\Carbon;

class LoyaltyHelper
{
    public static function getLoyaltyDiscount($userId)
    {
        // Last ticket
        $lastTicket = TicketIssue::where('user_id', $userId)->orderBy('id', 'desc')->first();

        if (!$lastTicket) {
            return 0; // No discount
        }

        $lastDate = Carbon::parse($lastTicket->created_at);
        $days = $lastDate->diffInDays(Carbon::now());

        // Loop all available discounts
        $discounts = LoyaltyDiscount::all();

        foreach ($discounts as $disc) {
            list($min, $max) = explode('-', $disc->days_range);

            if ($days >= $min && $days <= $max) {
                return $disc->discount_percent;
            }
        }

        return 0;
    }
}
