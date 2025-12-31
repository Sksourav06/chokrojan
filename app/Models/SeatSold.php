<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatSold extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'seat_number',
        'from_station_id',
        'to_station_id',
        'fare',
        'ticket_issue_id',
        'status',
        'passenger_name',
        'passenger_mobile',
        'gender',
        'boarding_counter_id',
        'dropping_counter_id',
        'from_station_id',
        'to_station_id',
        'fare',
    ];

    // --- Relationships ---

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function ticketIssue()
    {
        return $this->belongsTo(TicketIssue::class, 'ticket_issue_id');
    }

    // ✅ FIX 1: Boarding Counter Relationship
    public function boardingCounter()
    {
        return $this->belongsTo(Counter::class, 'boarding_counter_id');
    }

    // ✅ FIX 2: Dropping Counter Relationship
    public function droppingCounter()
    {
        return $this->belongsTo(Counter::class, 'dropping_counter_id');
    }

    // ✅ FIX 3: Station Relationships (Trip Sheet এ লাগতে পারে)
    public function fromStation()
    {
        return $this->belongsTo(Station::class, 'from_station_id');
    }

    public function toStation()
    {
        return $this->belongsTo(Station::class, 'to_station_id');
    }

    public function ticket()
    {
        return $this->belongsTo(TicketIssue::class, 'ticket_issue_id', 'id');
    }

}