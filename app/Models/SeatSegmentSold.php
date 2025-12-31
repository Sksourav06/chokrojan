<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeatSegmentSold extends Model
{
    use HasFactory;

    // মডেলটি যে টেবিল ব্যবহার করবে
    protected $table = 'seat_segment_solds';

    protected $fillable = [
        'schedule_id',
        'seat_number',
        'ticket_issue_id',
        'from_station_id',
        'to_station_id',
        'from_sequence',
        'to_sequence',
    ];

    // রিলেশনশিপ (ঐচ্ছিক, তবে ভালো প্র্যাকটিস)
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function ticket()
    {
        return $this->belongsTo(TicketIssue::class, 'ticket_issue_id');
    }

    public function fromStation()
    {
        return $this->belongsTo(Station::class, 'from_station_id');
    }

    public function toStation()
    {
        return $this->belongsTo(Station::class, 'to_station_id');
    }
}