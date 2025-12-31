<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleOnDay extends Model
{
    protected $fillable = [
        'schedule_id',
        'from_date',
        'to_date',
        'start_time',
        'weekdays',
    ];

    protected $casts = [
        'weekdays' => 'array',
    ];
}
