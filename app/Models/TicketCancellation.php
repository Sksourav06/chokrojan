<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCancellation extends Model
{
    use HasFactory;

    protected $table = 'ticket_cancellations'; // optional if table name follows convention

    protected $fillable = [
        'ticket_id',
        'cancelled_by',
        'cancelled_at',
        'reason',
        'refund_amount',
    ];

    public function ticket()
    {
        return $this->belongsTo(TicketIssue::class, 'ticket_id');
    }

    public function cancelledByUser()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}

