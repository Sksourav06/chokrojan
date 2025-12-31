<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ticket_segments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('origin_station_id');
            $table->unsignedBigInteger('destination_station_id');
            $table->string('seat_no', 20);
            $table->enum('status', ['available', 'booked', 'sold'])->default('available');
            $table->timestamps();

            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('origin_station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->foreign('destination_station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->index(['schedule_id','seat_no','origin_station_id','destination_station_id'], 'seg_lookup_index');
        });

        // ✅ Auto-generate segments for all existing schedules
        $routeStations = [
            1 => [1, 2, 3],      // Example: Route 1 stations by ID order
            2 => [4, 5, 6, 7],   // Add your actual route_id => station_id sequence here
        ];

        foreach (\DB::table('schedules')->get() as $schedule) {
            $stations = $routeStations[$schedule->route_id] ?? null;
            if (!$stations || count($stations) < 2) continue;

            $seatLayout = \DB::table('seat_layouts')->where('id', $schedule->seat_layout_id)->first();
            $totalSeats = $seatLayout->total_seats ?? 0;
            if ($totalSeats < 1) continue;

            for ($s = 1; $s <= $totalSeats; $s++) {
                $seatNo = "S{$s}";

                for ($i = 0; $i < count($stations)-1; $i++) {
                    $origin = $stations[$i];
                    $destination = $stations[$i+1];

                    \DB::table('ticket_segments')->insert([
                        'ticket_id' => null,
                        'schedule_id' => $schedule->id,
                        'origin_station_id' => $origin,
                        'destination_station_id' => $destination,
                        'seat_no' => $seatNo,
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('ticket_segments');
    }
};
