<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Counter;
use App\Models\Schedule;
use App\Models\TicketIssueSeat;
use App\Models\User;
use App\Models\Station; // Added missing use statement

class TicketIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'invoice_no',
        'issue_date',

        'from_station_id',
        'to_station_id',

        'customer_name',
        'customer_mobile',
        'passenger_email',

        // Note: 'seat_numbers' and 'seats_count' are often redundant 
        // if 'seats' relation is used, but kept as per your schema.
        'seat_numbers',
        'seats_count',

        'sub_total',
        'discount_amount',
        'service_charge',
        'goods_charge',
        'callerman_commission',
        'fare', // Fare per seat or average fare
        'grand_total',
        'issue_counter_id',
        'payment_method',
        'issued_by', // Maps to the User model
        'boarding_counter_id',
        'dropping_counter_id',
        'pnr_no',
        'status_label',
        'gender',
        'ticket_issue_id',
        'leg_start_station_id',
        'leg_end_station_id',
        'journey_date',
        'is_loyalty_discount_applied',
        'journey_date',
        'counter_commission_amount',
    ];

    protected $casts = [
        'issue_date' => 'datetime',
        'journey_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    // --- Relationships for Ticket Data Loading (Modal Header) ---

    // **FIXED/CONFIRMED**: Accesses the departure station name ($ticket->fromStation->name)
    public function fromStation()
    {
        return $this->belongsTo(Station::class, 'from_station_id');
    }

    // **FIXED/CONFIRMED**: Accesses the arrival station name ($ticket->toStation->name)
    public function toStation()
    {
        return $this->belongsTo(Station::class, 'to_station_id');
    }

    // **CONFIRMED**: Accesses trip details (coach, time) via Schedule model ($ticket->schedule->start_time)
    public function schedule()
    {
        return $this->belongsTo(Schedule::class); // Foreign key 'schedule_id' assumed
    }

    // **CONFIRMED**: Accesses the agent/user who issued the ticket ($ticket->issuedBy->name)
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // --- Relationships for Counter and Seat Information ---

    // **CONFIRMED**: Boarding counter details ($ticket->boardingCounter->name)
    public function boardingCounter()
    {
        return $this->belongsTo(Counter::class, 'boarding_counter_id');
    }

    // **CONFIRMED**: Dropping counter details ($ticket->droppingCounter->name)
    public function droppingCounter()
    {
        return $this->belongsTo(Counter::class, 'dropping_counter_id');
    }

    // **CONFIRMED**: Links to individual seat records (used in loops/calculations)
    // public function seats()
    // {
    //     return $this->hasMany(TicketIssueSeat::class);
    // }
    // app/Models/TicketIssue.php
    public function counter()
    {
        return $this->belongsTo(Counter::class, 'issued_from_counter_id'); // counter_id বা আপনার table column নাম
    }
    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }

    public function issueCounter()
    {
        return $this->belongsTo(Counter::class, 'issue_counter_id');
    }

    public function seatSold()
    {
        return $this->hasMany(SeatSold::class, 'ticket_issue_id');
    }

    // In TicketIssue model
    public function seats()
    {
        // Use just the unqualified class name, not App\Models\Seat::class
        return $this->hasMany(Seat::class, 'ticket_issue_id');
    }

    public function cancellations()
    {
        return $this->hasMany(TicketCancellation::class);
    }

    public function route()
    {
        return $this->belongsTo(\App\Models\Route::class, 'route_id');
    }
    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'ticket_issue_id');
    }



}