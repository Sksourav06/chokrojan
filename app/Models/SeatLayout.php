<?php

// app/Models/SeatLayout.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_seats',
        'rows',
        'columns',
        'deck_type',
        'class_types',
        'seat_map_config',
        'is_active',
    ];

    // ⭐ CRITICAL FIX: Ensure casting is defined for JSON fields ⭐
    protected $casts = [
        'class_types' => 'array',
        'seat_map_config' => 'array', // Must be cast to 'array'
        'is_active' => 'boolean',
        'layout' => 'array',
    ];

    public function seats()
    {
        return $this->hasMany(Seat::class, 'layout_id');
    }
}