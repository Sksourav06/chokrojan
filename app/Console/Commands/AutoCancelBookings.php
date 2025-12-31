<?php

namespace App\Console\Commands; // নিশ্চিত করুন এটি সঠিক Namespace

use Illuminate\Console\Command;
use App\Models\TicketIssue;
use App\Models\Schedule;
use App\Models\SystemSetting; // SystemSetting মডেল ইমপোর্ট করা হয়েছে
use App\Models\SeatSold; // সিট মুক্ত করার জন্য এই মডেলটি দরকার
use App\Models\TicketCancellation; // ক্যানসেলেশন লগ করার জন্য (ঐচ্ছিক, তবে ভালো প্র্যাকটিস)
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ⚠️ এখানে ক্লাসের ডিক্লারেশন যোগ করা হলো
class AutoCancelBookings extends Command
{
    protected $signature = 'bookings:auto-cancel';
    protected $description = 'Automatically cancels booked (unpaid) tickets before schedule departure time.';

    public function handle()
    {
        // 1. **CRITICAL FIX:** Get the dynamic cancellation threshold from the column

        // Find the single configuration row
        $setting = SystemSetting::first();

        // Use the column 'booking_cancel_time'. Default to 60 minutes if row or value is missing/zero.
        $cancelThresholdMinutes = ($setting && $setting->booking_cancel_time > 0)
            ? (int) $setting->booking_cancel_time
            : 60;

        // Calculate the cancellation time: Departure Time <= (Now + Threshold Minutes)
        $cancellationTime = Carbon::now()->addMinutes($cancelThresholdMinutes);
        $cancelledCount = 0;

        $this->info("Running auto-cancellation with threshold: {$cancelThresholdMinutes} minutes.");

        // 2. Find schedules that are due to start within the threshold
        $dueSchedules = Schedule::where('start_time', '<=', $cancellationTime)
            ->where('start_time', '>', Carbon::now())
            ->pluck('id');

        if ($dueSchedules->isEmpty()) {
            $this->info('No schedules found due for auto-cancellation.');
            return 0;
        }

        // 3. Find 'Booked' (unpaid) tickets for those schedules
        DB::beginTransaction();
        try {
            $ticketsToCancel = TicketIssue::whereIn('schedule_id', $dueSchedules)
                ->where('status_label', 'Booked')
                ->where('status', 'active')
                ->get();

            foreach ($ticketsToCancel as $ticket) {

                // Update Ticket Status to Cancelled
                $ticket->update([
                    'status' => 'cancelled',
                    'status_label' => 'Cancelled (Auto)',
                    'cancelled_at' => Carbon::now(),
                    'cancelled_by' => 0,
                ]);

                // Release seats associated with this ticket (assuming SeatSold model)
                \App\Models\SeatSold::where('ticket_issue_id', $ticket->id)->delete();

                $cancelledCount++;
            }

            DB::commit();
            $this->info("Successfully auto-cancelled {$cancelledCount} booked tickets using {$cancelThresholdMinutes} minutes threshold.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Auto-Cancellation Failed: " . $e->getMessage());
            $this->error("Auto-Cancellation Failed. Check logs.");
        }
        return 0;
    }
}