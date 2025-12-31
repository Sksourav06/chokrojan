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
            $table->string('status')->default('active')->after('grand_total'); // active/cancelled
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('status'); // user who cancelled
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by'); // cancellation date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancelled_by', 'cancelled_at']);
        });
    }
};
