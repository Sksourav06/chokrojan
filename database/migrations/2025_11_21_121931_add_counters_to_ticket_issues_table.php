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
            $table->unsignedBigInteger('boarding_counter_id')->nullable()->after('schedule_id');
            $table->unsignedBigInteger('dropping_counter_id')->nullable()->after('boarding_counter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropColumn(['boarding_counter_id', 'dropping_counter_id']);
        });
    }
};
