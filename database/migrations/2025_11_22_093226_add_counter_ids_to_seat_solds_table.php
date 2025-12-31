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
            $table->foreignId('boarding_counter_id')->nullable()->after('status')->constrained('counters');
            $table->foreignId('dropping_counter_id')->nullable()->after('boarding_counter_id')->constrained('counters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_solds', function (Blueprint $table) {
            $table->dropForeign(['boarding_counter_id']);
            $table->dropForeign(['dropping_counter_id']);
            $table->dropColumn(['boarding_counter_id', 'dropping_counter_id']);
        });
    }
};
