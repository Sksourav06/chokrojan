<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'offer_name',
        'discount_amount',
        'min_fare',
        'max_fare',
        'start_date',
        'end_date',
        'schedule_id',
        'route_id',
        'bus_type',
        'is_active',
    ];

    // ... আগের casts এবং অন্যান্য কোড ...
    protected $casts = [
        'start_date' => 'date', // এটি স্ট্রিংকে কার্বন অবজেক্ট বানিয়ে দেবে
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
    /**
     * মাস্টার শিডিউলের সাথে রিলেশন (trip_code পাওয়ার জন্য)
     */
    public function masterSchedule()
    {
        // এখানে 'schedule_id' হলো অফার টেবিলের কলাম যা master_schedules এর 'id' কে রেফার করে
        return $this->belongsTo(MasterSchedule::class, 'schedule_id');
    }

    // আপনার মডেলে যদি নিচের রিলেশনগুলো না থাকে তবে এগুলোও যোগ করে নিন
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}