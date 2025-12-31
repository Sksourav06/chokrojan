<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FareStationPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'origin_station_id',
        'destination_station_id',
        'fare_id',
        'price',
    ];

    // Main relationships
    public function fromStation()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function toStation()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    // Alias relationships (to prevent undefined relationship error)
    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }

    public function fare()
    {
        return $this->belongsTo(Fare::class);
    }
}
