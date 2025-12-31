<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zone_id',
        'status',
    ];

    /**
     * Get the Zone that the Route belongs to.
     */
    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
    /**
     * Get the Stations associated with the Route in sequence.
     */
    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'route_station_sequence')
            ->withPivot('sequence_order', 'required_time')
            ->orderBy('pivot_sequence_order');
    }

    // public function routeStationSequences()
    // {
    //     return $this->hasMany(\App\Models\RouteStationSequence::class, 'route_id', 'id')
    //         ->orderBy('sequence_order', 'asc');
    // }

    public function routeStationSequences()
    {
        return $this->hasMany(\App\Models\RouteStationSequence::class, 'route_id', 'id')
            ->orderBy('sequence_order', 'asc');
    }

    public function startStation()
    {
        return $this->belongsTo(Station::class, 'start_station_id');
    }

    public function endStation()
    {
        return $this->belongsTo(Station::class, 'end_station_id');
    }

    // in Route model
    public function fareStationPrices()
    {
        return $this->hasMany(FareStationPrice::class, 'route_id');
    }

    // public function stations()
    // {
    //     return $this->hasMany(RouteStation::class, 'route_id')->orderBy('position');
    // }


}