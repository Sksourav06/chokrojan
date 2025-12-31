<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'make_year',
        'model_name',
        'bus_type',
        'seat_layout_id', // Foreign key to seat_layouts table
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'make_year' => 'integer',
        'status' => 'string',
    ];

    /**
     * Define the relationship to the SeatLayout model.
     */
    public function seatLayout(): BelongsTo
    {
        return $this->belongsTo(SeatLayout::class, 'seat_layout_id');
    }
}