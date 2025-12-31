<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'days_threshold',    // কত দিনের মধ্যে টিকিট কাটলে ডিসকাউন্ট
        'discount_amount',   // ডিসকাউন্ট টাকা
        'start_date',        // ডিসকাউন্টের শুরু তারিখ
        'end_date',          // ডিসকাউন্টের শেষ তারিখ
        'is_active'          // চালু/বন্ধ
    ];
}
