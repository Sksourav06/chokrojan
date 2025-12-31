<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('staff_designations')->insert([
            ['name' => 'Driver', 'description' => 'Bus Driver', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'description' => 'Route Supervisor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Helper', 'description' => 'Driver Helper', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
