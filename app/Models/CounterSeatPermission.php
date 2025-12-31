<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CounterSeatPermission extends Model
{
    use HasFactory;

    protected $fillable = ['counter_id', 'schedule_id', 'blocked_seats'];

    protected $casts = [
        'blocked_seats' => 'array',
    ];
}
