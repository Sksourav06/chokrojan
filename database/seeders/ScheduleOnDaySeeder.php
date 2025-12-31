<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScheduleOnDay;

class ScheduleOnDaySeeder extends Seeder
{
    public function run()
    {
        ScheduleOnDay::create([
            'schedule_id' => 1,
            'from_date' => '2025-11-01',
            'to_date' => '2025-11-30',
            'start_time' => '08:00',
            'weekdays' => ["Sunday", "Monday", "Wednesday"],
        ]);
    }
}
