<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'counter_id',
        'station_id',
        'schedule_id',
        'can_sell',
        'can_hold',
        'can_block',
    ];

    protected $casts = [
        'blocked_seats' => 'array',
    ];

    // Optional relationships
    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
