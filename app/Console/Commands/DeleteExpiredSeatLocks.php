<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SeatLock; // আপনার মডেলটি ইমপোর্ট করুন
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
    protected $description = 'Deletes seat lock records that have passed their expiration time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // বর্তমান সময়ের চেয়ে পুরনো বা সমানExpires_at এর সমস্ত লক খুঁজুন
        $deletedCount = SeatLock::where('expires_at', '<=', Carbon::now())
            ->delete();

        $this->info("Successfully deleted {$deletedCount} expired seat locks.");
        return 0;
    }
}