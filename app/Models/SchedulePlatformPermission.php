<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulePlatformPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'platform_id',
        'from_date',
        'to_date',
        'blocked_seats',
        'status',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'blocked_seats' => 'array',
    ];

    // public function platform()
    // {
    //     return $this->belongsTo(Platform::class);
    // }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }
}
