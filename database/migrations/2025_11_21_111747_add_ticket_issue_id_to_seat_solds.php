<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seat_solds', function (Blueprint $table) {
            if (!Schema::hasColumn('seat_solds', 'ticket_issue_id')) {
                $table->foreignId('ticket_issue_id')->nullable()->after('schedule_id')->constrained('ticket_issues')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_solds', function (Blueprint $table) {
            if (Schema::hasColumn('seat_solds', 'ticket_issue_id')) {
                $table->dropConstrainedForeignId('ticket_issue_id');
            }
        });
    }
};
