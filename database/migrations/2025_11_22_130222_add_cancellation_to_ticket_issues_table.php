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
        Schema::table('ticket_issues', function (Blueprint $table) {
            // Only add 'cancelled_by' if it doesn't exist
            if (!Schema::hasColumn('ticket_issues', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable()->after('status');
                $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
            }

            // Only add 'cancelled_at' if it doesn't exist
            if (!Schema::hasColumn('ticket_issues', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }

            // Skip adding 'status' because it already exists
        });
    }

    public function down(): void
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['status', 'cancelled_by', 'cancelled_at']);
        });
    }
};
