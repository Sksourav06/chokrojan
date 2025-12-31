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
        Schema::table('route_station_sequence', function (Blueprint $table) {
            // Add the time column to store the required time to reach this station from the previous one.
            // Using TIME type is ideal for HH:MM format (time duration).
            $table->time('required_time')->default('00:00')->after('sequence_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_station_sequence', function (Blueprint $table) {
            $table->dropColumn('required_time');
        });
    }
};