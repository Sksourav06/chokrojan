<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleCounter extends Model
{
    use HasFactory;

    protected $table = 'schedule_counters';

    protected $fillable = [
        'schedule_id',
        'station_id',
        'counter_id',
        'time',
        'from_date',
        'to_date',
    ];


    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }
}
