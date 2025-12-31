<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Models\MasterSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MigrateSchedulesToMaster extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-schedules-to-master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates unique schedules from the schedules table to the master_schedules table and updates foreign keys.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Master Schedule এ ডেটা মাইগ্রেশন শুরু হচ্ছে...");

        // ১. schedules টেবিল থেকে bus_id, route_id এবং start_time_nextday এর উপর ভিত্তি করে ইউনিক শিডিউলগুলো খুঁজে বের করুন
        $uniqueScheduleIdentities = Schedule::select(
            'name',
            'bus_id',
            'route_id',
            'start_station_id',
            'end_station_id',
            'bus_type',
            'start_time_nextday',
        )
            // bus_id, route_id, এবং start_time_nextday দ্বারা গ্রুপ করে মাস্টার ট্রিপের ধরন চিহ্নিত করা হচ্ছে
            ->groupBy('bus_id', 'route_id', 'start_time_nextday')
            ->get();

        $migratedCount = 0;

        foreach ($uniqueScheduleIdentities as $identity) {

            // ইউনিক ট্রিপের start_time এবং end_time পাওয়ার জন্য একটি উদাহরণ শিডিউল খুঁজে নিন
            $masterTripExample = Schedule::where('bus_id', $identity->bus_id)
                ->where('route_id', $identity->route_id)
                ->where('start_time_nextday', $identity->start_time_nextday)
                ->oldest('start_time')
                ->first();

            if (!$masterTripExample) {
                continue;
            }

            // start_time এবং end_time স্ট্রিং হলে Carbon অবজেক্টে রূপান্তর করা
            $startTime = $masterTripExample->start_time;
            $endTime = $masterTripExample->end_time;

            if (is_string($startTime)) {
                $startTime = Carbon::parse($startTime);
            }
            if (is_string($endTime)) {
                $endTime = Carbon::parse($endTime);
            }


            // 🚨 ফিক্স: MasterSchedule::firstOrCreate() ব্যবহার করা হলো ডুপ্লিকেট এন্ট্রি এড়ানোর জন্য।
            $master = MasterSchedule::firstOrCreate(
                // Search Criteria (Unique trip code)
                ['trip_code' => $identity->name],

                // Data to create if not found (নতুন ডেটা তৈরি করতে হবে)
                [
                    'bus_id' => $identity->bus_id,
                    'route_id' => $identity->route_id,
                    'start_station_id' => $identity->start_station_id,
                    'end_station_id' => $identity->end_station_id,
                    'bus_type' => $identity->bus_type,
                    'start_time_nextday' => $identity->start_time_nextday,
                    'start_time_only' => $startTime->format('H:i:s'),
                    'end_time_only' => $endTime->format('H:i:s'),
                ],
            );

            // ৩. মূল schedules টেবিল আপডেট করুন: এই MasterTrip এর সাথে যুক্ত সকল শিডিউলে master_schedule_id সেট করুন
            Schedule::where('bus_id', $identity->bus_id)
                ->where('route_id', $identity->route_id)
                ->where('start_time_nextday', $identity->start_time_nextday)
                ->update(['master_schedule_id' => $master->id]);

            $migratedCount++;
        }

        $this->info("সফলভাবে {$migratedCount} টি ইউনিক মাস্টার শিডিউল মাইগ্রেট করা হয়েছে এবং শিডিউল টেবিল আপডেট করা হয়েছে।");
        return 0;
    }
}