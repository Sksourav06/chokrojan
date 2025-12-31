<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
class Passenger extends Model
{
    use HasFactory, HasApiTokens;
    protected $appends = ['name'];
    // ডাটা ইনসার্ট করার জন্য অনুমোদিত কলামসমূহ
    protected $fillable = [
        'ticket_issue_id',
        'first_name',
        'last_name',
        'mobile_number',
        'gender',
        'email',
        'password',
        'street_address',
        'city',
        'zip_code',
        'seat_number',
        'fare',
    ];

    /**
     * একটি প্যাসেঞ্জার একটি টিকেটের অধীনে থাকে
     */
    public function ticket()
    {
        return $this->belongsTo(TicketIssue::class, 'ticket_issue_id');
    }



    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}