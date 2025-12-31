<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Station;
use Carbon\Carbon;
class DashboardController extends Controller
{
    public function index()
    {
        // Active counters count
        $activeCounters = Counter::where('status', 'active')->count();
        $runningCounters = Counter::where('status', 'running')->count();
        $activeRoutes = Route::where('status', 'active')->count();
        $activeStations = Station::where('status', 'active')->count();
        // Daily Schedules (আজকের date অনুযায়ী)
        $today = Carbon::today()->toDateString();
        $dailySchedules = Schedule::whereDate('start_time', $today)->count();

        // Running Schedules (আজকের এবং এখনও শেষ হয়নি এমন)
        $now = Carbon::now();
        $runningSchedules = Schedule::whereDate('start_time', $today)
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->count();
        $dailyTrips = Schedule::whereDate('start_time', $today)->count();
        $today = Carbon::today();
        $currentMonth = Carbon::now();
        return view('admin.dashboard', compact(
            'activeCounters',
            'runningCounters',
            'activeRoutes',
            'activeStations',
            'dailySchedules',
            'runningSchedules',
            'dailyTrips',
            'today',
            'currentMonth',

        ));
    }
}

