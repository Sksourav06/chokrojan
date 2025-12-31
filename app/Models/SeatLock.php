<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatLock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_id',
        'seat_number',
        'counter_id',
        'expires_at',
    ];

    public function counter()
    {
        return $this->belongsTo(Counter::class, 'counter_id');
    }
}