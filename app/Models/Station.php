<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'city',
        'district',
        'division',
        'sequence',
    ];

    protected $casts = [
        'status' => 'string', // Although ENUM is used, casting as string is safer
    ];

    public function counters()
    {
        return $this->hasMany(Counter::class, 'station_id');
    }
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_station_sequence', 'station_id', 'route_id')
            ->withPivot('sequence_order', 'required_time');
    }
    public function originFareStationPrices()
    {
        return $this->hasMany(FareStationPrice::class, 'origin_station_id');
    }

    public function destinationFareStationPrices()
    {
        return $this->hasMany(FareStationPrice::class, 'destination_station_id');
    }
}