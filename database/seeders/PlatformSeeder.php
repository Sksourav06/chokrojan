<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Platform;
class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = [
            ['name' => 'ShareTrip', 'slug' => 'sharetrip', 'logo' => 'sharetrip.png'],
            ['name' => 'BDTickets', 'slug' => 'bdtickets', 'logo' => 'bdtickets.png'],
            ['name' => 'Shohoz', 'slug' => 'shohoz', 'logo' => 'shohoz.png'],
            ['name' => 'Obhai', 'slug' => 'obh.com', 'logo' => 'obh.png'],
            ['name' => 'GoZayaan', 'slug' => 'gozayaan', 'logo' => 'gozayaan.png'],
        ];

        foreach ($platforms as $p) {
            Platform::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
