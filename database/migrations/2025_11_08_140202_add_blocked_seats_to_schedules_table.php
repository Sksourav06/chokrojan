<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // যদি আগেই না থাকে, তাহলে JSON কলাম যুক্ত করো
            if (!Schema::hasColumn('schedules', 'blocked_seats')) {
                $table->json('blocked_seats')->nullable()->after('seat_layout_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('blocked_seats');
        });
    }
};

