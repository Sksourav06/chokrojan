<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CounterPermission;

class CounterPermissionSeeder extends Seeder
{
    public function run()
    {
        CounterPermission::create([
            'counter_id' => 1,
            'station_id' => 1,
            'schedule_id' => 1,
            'can_sell' => true,
            'can_hold' => true,
            'can_block' => false,
        ]);

        CounterPermission::create([
            'counter_id' => 2,
            'station_id' => 1,
            'schedule_id' => 1,
            'can_sell' => true,
            'can_hold' => false,
            'can_block' => true,
        ]);
    }
}
