<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'layout_id',
        'seat_no',
        'is_reserved',
        'status',
    ];

    public function layout()
    {
        return $this->belongsTo(SeatLayout::class, 'layout_id');
    }
    public function ticketIssue()
    {
        return $this->belongsTo(TicketIssue::class);
    }
}
