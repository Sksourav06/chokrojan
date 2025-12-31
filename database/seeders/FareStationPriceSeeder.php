<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RouteStationSequence;
use App\Models\FareStationPrice;

class FareStationPriceSeeder extends Seeder
{
    public function run(): void
    {
        $routeId = 1; // your current route
        $fareId = 1;  // adjust if you have multiple fares

        $stations = RouteStationSequence::where('route_id', $routeId)
            ->orderBy('sequence_order')
            ->pluck('station_id')
            ->toArray();

        foreach ($stations as $i => $origin) {
            for ($j = $i + 1; $j < count($stations); $j++) {
                $destination = $stations[$j];

                FareStationPrice::updateOrCreate(
                    [
                        'fare_id' => $fareId,
                        'route_id' => $routeId,
                        'origin_station_id' => $origin,
                        'destination_station_id' => $destination,
                    ],
                    ['price' => 100 + ($j - $i) * 50],
                );
            }
        }
    }
}
