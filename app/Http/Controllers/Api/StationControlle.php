<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationControlle extends Controller
{
    public function index()
    {
        // সরাসরি অ্যারে রিটার্ন করুন যাতে Next.js এ map করতে সুবিধা হয়
        return response()->json(Station::where('status', 'active')->pluck('name'));
    }
}
