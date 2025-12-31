<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RouteStationSequence;

class RouteStationSequenceSeeder extends Seeder
{
    public function run(): void
    {
        // Update sequence order for existing stations
        RouteStationSequence::where('station_id', 6)->update(['sequence_order' => 2]);
        RouteStationSequence::where('station_id', 2)->update(['sequence_order' => 3]);
        RouteStationSequence::where('station_id', 3)->update(['sequence_order' => 4]);
    }
}
