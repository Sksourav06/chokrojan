<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id', // Foreign key
        'name',
        'counter_type',
        'credit_limit',
        'credit_balance',
        'permitted_credit',
        'status',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_balance' => 'decimal:2',
        'permitted_credit' => 'decimal:2',

    ];

    /**
     * Get the Station that owns the Counter.
     */
    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    /**
     * The routes and commissions for this counter (Many-to-Many).
     * Requires the 'routes' table and 'counter_route_commissions' pivot table.
     */
    public function routes(): BelongsToMany
    {
        // Note: You must create the 'Route' model and 'routes' table later.
        return $this->belongsToMany(Route::class, 'counter_route_commissions')
            ->withPivot('ac_commission', 'non_ac_commission')
            ->withTimestamps();
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'counter_user', 'counter_id', 'user_id');
    }
}