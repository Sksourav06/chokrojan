<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketSegment extends Model
{
    protected $fillable = [
        'ticket_id',
        'schedule_id',
        'origin_station_id',
        'destination_station_id',
        'seat_no',
        'status',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function originStation()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destinationStation()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }
}