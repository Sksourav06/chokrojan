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
        Schema::table('master_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('origin_station_id')->nullable();
            $table->unsignedBigInteger('destination_station_id')->nullable();

            // Adding foreign key constraints if necessary
            $table->foreign('origin_station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->foreign('destination_station_id')->references('id')->on('stations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_schedules', function (Blueprint $table) {
            $table->dropForeign(['origin_station_id']);
            $table->dropForeign(['destination_station_id']);
            $table->dropColumn(['origin_station_id', 'destination_station_id']);
        });
    }
};
