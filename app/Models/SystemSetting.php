<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        // COMMON
        'site_name',
        'logo',
        'booking_cancel_time',
        'permitted_seat_block_release_time',
        'advance_booking',
        'selected_seat_lifetime',
        'seat_cancel_allow',
        'previous_date_view_allow',
        'passenger_star_rating',
        'booking',
        'vip_booking',
        'goods_charge',
        'callerman_commission',
        'discount',
        'discount_show_in_ticket',

        // COUNTER
        'booking_lifetime',
        'counter_sales_allow_time',
        'counter_max_seat_per_ticket',
        'counter_cancel_allow',
        'counter_cancel_fine',
        'counter_cancel_allow_time',

        // ONLINE
        'online_booking_lifetime',
        'online_sales_disallow_time',
        'online_max_seat_per_ticket',
        'online_cancel_allow',
        'online_cancel_fine',
        'online_cancel_allow_time',

    ];
}
