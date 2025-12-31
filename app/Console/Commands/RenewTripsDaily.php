<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RenewTripsDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:renew-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew trips daily by creating or updating trip data for the next day.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');

        // আজকের schedule গুলো নাও
        $todaySchedules = \App\Models\Schedule::whereDate('start_time', $today)->get();

        if ($todaySchedules->isEmpty()) {
            $this->info('No trips found today. Nothing to renew.');
            return;
        }

        foreach ($todaySchedules as $schedule) {

            // Duplicate check: same bus + same route + same date
            $exists = \App\Models\Schedule::where('bus_id', $schedule->bus_id)
                ->where('route_id', $schedule->route_id)
                ->whereDate('start_time', $tomorrow)
                ->exists();

            if ($exists) {
                continue; // Duplicate creation হবে না
            }

            // নতুন start_time & end_time = same time next day
            $newStartTime = date(
                'Y-m-d H:i:s',
                strtotime($tomorrow . ' ' . $schedule->start_time),
            );

            $newEndTime = isset($schedule->end_time) ? date(
                'Y-m-d H:i:s',
                strtotime($tomorrow . ' ' . $schedule->end_time),
            ) : null;

            // Name unique করার জন্য date যোগ করা
            $name = $schedule->name . ' - ' . $tomorrow;

            // Create exact copy
            \App\Models\Schedule::create([
                'name' => $name,
                'route_tagline' => $schedule->route_tagline,
                'bus_id' => $schedule->bus_id,
                'route_id' => $schedule->route_id,
                'start_station_id' => $schedule->start_station_id,
                'end_station_id' => $schedule->end_station_id,
                'seat_layout_id' => $schedule->seat_layout_id,
                'blocked_seats' => $schedule->blocked_seats,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime,
                'start_time_nextday' => $schedule->start_time_nextday,
                'bus_type' => $schedule->bus_type,
                'status' => $schedule->status,
            ]);
        }

        $this->info('Tomorrow\'s trips cloned successfully.');
    }



}

