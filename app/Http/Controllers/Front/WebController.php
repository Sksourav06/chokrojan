<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        // ডাটাবেস থেকে সমস্ত স্টেশনের (শহর) নাম সংগ্রহ করা
        // Assuming your Station model has a 'name' column
        $search_cities = Station::orderBy('name')->pluck('name')->toArray();

        // ভিউতে ডেটা পাঠানো
        return view('front.welcome', compact('search_cities'));
    }
}
