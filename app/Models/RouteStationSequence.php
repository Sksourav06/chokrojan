<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStationSequence extends Model
{
    use HasFactory;

    protected $table = 'route_station_sequence';

    public $timestamps = false; // No created_at/updated_at
    protected $primaryKey = null; // No id column
    public $incrementing = false; // Not auto-incrementing

    protected $fillable = [
        'route_id',
        'station_id',
        'sequence_order',
        'required_time',

    ];


    // public function station()
    // {
    //     return $this->belongsTo(\App\Models\Station::class, 'station_id', 'id');
    // }
    public function station()
    {
        return $this->belongsTo(\App\Models\Station::class, 'station_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }


}
