<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeatLock;
use Carbon\Carbon;

class DeleteExpiredSeatLocks extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'locks:delete-expired';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Continuously deletes expired seat locks every second.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Seat Lock Cleaner Worker Started...");

        // ==========================================
        // Supervisor এর জন্য অসীম লুপ (Infinite Loop)
        // ==========================================
        while (true) {

            // ১. মেয়াদোত্তীর্ণ লক ডিলিট করা
            $deletedCount = SeatLock::where('expires_at', '<=', Carbon::now())->delete();

            // যদি কোনো ডাটা ডিলিট হয়, তবে লগে দেখাবে
            if ($deletedCount > 0) {
                $this->info("Deleted {$deletedCount} expired locks at " . Carbon::now()->toTimeString());
            }

            // ২. ১ সেকেন্ড অপেক্ষা করা (যাতে CPU ওভারলোড না হয়)
            sleep(1);
        }

        // এই লাইনটি টেকনিক্যালি কখনোই এক্সিকিউট হবে না কারণ লুপটি অসীম
        return 0;
    }
}