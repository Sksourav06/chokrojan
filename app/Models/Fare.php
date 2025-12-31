<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fare extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'route_id',
        'bus_type',
        'seat_layout_id',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Get the route associated with the fare.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the seat layout associated with the fare.
     */
    public function seatLayout(): BelongsTo
    {
        return $this->belongsTo(SeatLayout::class);
    }

    /**
     * Get the station prices associated with the fare.
     */
    public function stationPrices(): HasMany
    {
        return $this->hasMany(FareStationPrice::class);
    }

    /**
     * Get the schedule (if any) associated with the fare.
     * (This assumes you have a 'schedule_id' column on the fare table.)
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
