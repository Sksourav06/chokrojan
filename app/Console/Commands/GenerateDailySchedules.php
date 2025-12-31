<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use Carbon\Carbon;

class GenerateDailySchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedules:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates daily schedules based on a master template for the next 30 days.';

    public function handle()
    {
        // ১. মাস্টার টেমপ্লেট হিসেবে আজকের শিডিউলগুলিকে ধরছি।
        // বর্তমানে সময়: Nov 25, 2025
        $referenceDate = Carbon::today()->toDateString();

        // 💡 ফিক্স ১: এটি নিশ্চিত করবে যে আমরা শিডিউল তৈরির সময় মাস্টার ট্রিপের name পরিবর্তন করছি না।
        $masterSchedules = Schedule::whereDate('start_time', $referenceDate)->get();

        if ($masterSchedules->isEmpty()) {
            $this->error("মাস্টার শিডিউল হিসেবে ব্যবহার করার জন্য আজকের ({$referenceDate}) কোনো শিডিউল পাওয়া যায়নি।");
            return 0;
        }

        $tripsGenerated = 0;

        // ২. আগামী ৩০ দিনের জন্য লুপ চালানো
        $daysToGenerate = 30;
        for ($i = 1; $i <= $daysToGenerate; $i++) {
            $targetDate = Carbon::today()->addDays($i)->toDateString();

            // যদি শিডিউল আগে থেকেই তৈরি থাকে, তবে এড়িয়ে যাবে
            if (Schedule::whereDate('start_time', $targetDate)->exists()) {
                $this->warn("{$targetDate} এর শিডিউল আগে থেকেই আছে। এড়িয়ে যাওয়া হলো।");
                continue;
            }

            // ৩. মাস্টার শিডিউল থেকে নতুন শিডিউল তৈরি
            foreach ($masterSchedules as $master) {

                // 💡 ফিক্স ২: end_time যদি শুধু TIME স্ট্রিং হিসেবে সেভ থাকে, তবে তা হ্যান্ডেল করা
                // নিশ্চিত করুন যে start_time একটি Carbon Instance (DATETIME)
                $masterStartTime = $master->start_time instanceof Carbon ? $master->start_time : Carbon::parse($master->start_time);

                // সময় অংশটি নিন (যেমন: '16:00:00')
                $timePart = $masterStartTime->format('H:i:s');

                // নতুন তারিখের সাথে সময় জুড়ে দিয়ে start_time তৈরি করুন
                $newStartTime = Carbon::parse($targetDate . ' ' . $timePart);

                // ট্রিপের মোট সময়কাল (Duration) মিনিট-এ বের করুন
                // এটি সবচেয়ে নিরাপদ পদ্ধতি, যদি end_time মাস্টার ট্রিপে সঠিকভাবে সেভ থাকে
                $durationInMinutes = $masterStartTime->diffInMinutes($master->end_time);

                // নতুন start_time এর সাথে সময়কাল যোগ করে নতুন end_time বের করুন
                $newEndTime = $newStartTime->copy()->addMinutes($durationInMinutes);

                // মধ্যরাত পার হওয়ার লজিক
                if ($master->start_time_nextday && $newEndTime < $newStartTime) {
                    $newEndTime = $newEndTime->addDay();
                }

                // 💡 ফিক্স ৩: name কলামের পরিবর্তন রোধ করা এবং সঠিক মান পাস করা
                $master->replicate()->fill([
                    'name' => $master->name, // মাস্টার ট্রিপের নাম কপি করা
                    'start_time' => $newStartTime,
                    'end_time' => $newEndTime,
                    // অন্যান্য foreign key গুলো স্বয়ংক্রিয়ভাবে replicate দ্বারা কপি হবে
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])->save();

                $tripsGenerated++;
            }
        }

        $this->info("মোট {$tripsGenerated} টি নতুন শিডিউল তৈরি সম্পন্ন হয়েছে।");
        return 0;
    }
}