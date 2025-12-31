<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'route_tagline',
        'coach_number',
        'route_id',
        'start_time',
        'end_time',
        'start_station_id',
        'end_station_id',
        'bus_id',
        'seat_layout_id',
        'bus_type',
        'status',
        'start_time_nextday',
        'master_schedule_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'start_time_nextday' => 'boolean',
    ];
    // --- Relationships ---

    // Route relationship
    // public function route(): BelongsTo
    // {
    //     return $this->belongsTo(Route::class);
    // }

    // Bus relationship
    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    // Primary relationship name used in the 'edit' method
    public function seatPlan(): BelongsTo
    {
        return $this->belongsTo(SeatLayout::class, 'seat_layout_id');
    }

    // Alias relationship name used in the 'index' method to resolve the error
    public function seatLayout(): BelongsTo
    {
        return $this->seatPlan();
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function startStation()
    {
        return $this->belongsTo(\App\Models\Station::class, 'start_station_id');
    }

    public function endStation()
    {
        return $this->belongsTo(\App\Models\Station::class, 'end_station_id');
    }

    public function seat_layout()
    {
        return $this->belongsTo(\App\Models\SeatLayout::class, 'seat_layout_id');
    }
    // public function soldSeats()
    // {
    //     return $this->hasMany(SeatSold::class, 'schedule_id');
    // }


    public function routeFares()
    {
        return $this->hasMany(\App\Models\FareStationPrice::class, 'route_id', 'route_id');
    }
    // Trip.php
    public function ticketIssues()
    {
        return $this->hasMany(TicketIssue::class, 'schedule_id', 'id');
    }

    public function fromStation()
    {
        return $this->belongsTo(\App\Models\Station::class, 'origin_station_id');
    }

    // Relationship to the destination/end station
    public function toStation()
    {
        return $this->belongsTo(\App\Models\Station::class, 'destination_station_id');
    }

    // Optional: Relationship to route
    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }
    public function stations()
    {
        return $this->belongsToMany(Station::class, 'schedule_stations')
            ->withPivot('sequence')
            ->orderBy('pivot_sequence');
    }


    public function masterSchedule(): BelongsTo
    {
        return $this->belongsTo(MasterSchedule::class, 'master_schedule_id');
    }

    public function soldSeats()
    {
        // This relation should fetch all relevant TicketIssue records for the trip (schedule)
        return $this->hasMany(TicketIssue::class, 'schedule_id', 'id')
            ->whereIn('status_label', ['Sold', 'Booked']); // Only fetch relevant statuses
    }

    public function ticketSegments()
    {
        return $this->hasMany(TicketSegment::class, 'schedule_id');
    }

    public function tickets()
    {
        return $this->hasMany(\App\Models\TicketIssue::class, 'schedule_id', 'id');
    }

    public function permissions()
    {
        return $this->hasMany(\App\Models\SchedulePlatformPermission::class, 'schedule_id');
    }


}