<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RouteStation extends Model
{
    protected $table = 'route_stations';
    protected $fillable = ['route_id', 'station_id', 'position'];
}
