<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TicketIssueSeat extends Model
{
    use HasFactory;


    protected $fillable = ['ticket_issue_id', 'seat_sold_id', 'seat_number', 'fare'];


    public function ticket()
    {
        return $this->belongsTo(TicketIssue::class, 'ticket_issue_id');
    }


    public function seatSold()
    {
        return $this->belongsTo(SeatSold::class, 'seat_sold_id');
    }


}