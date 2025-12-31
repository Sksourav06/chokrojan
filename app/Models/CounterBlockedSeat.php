<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterBlockedSeat extends Model
{
    use HasFactory;

    protected $fillable = ['schedule_id', 'counter_id', 'blocked_seats'];

    protected $casts = [
        'blocked_seats' => 'array',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
