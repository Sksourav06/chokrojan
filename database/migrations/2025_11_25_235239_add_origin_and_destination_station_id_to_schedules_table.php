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
        Schema::table('schedules', function (Blueprint $table) {
            // Add the origin_station_id and destination_station_id if they do not exist already
            if (!Schema::hasColumn('schedules', 'origin_station_id')) {
                $table->unsignedBigInteger('origin_station_id')->nullable();
                $table->foreign('origin_station_id')->references('id')->on('stations')->onDelete('cascade');
            }

            if (!Schema::hasColumn('schedules', 'destination_station_id')) {
                $table->unsignedBigInteger('destination_station_id')->nullable();
                $table->foreign('destination_station_id')->references('id')->on('stations')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'origin_station_id')) {
                $table->dropForeign(['origin_station_id']);
                $table->dropColumn('origin_station_id');
            }

            if (Schema::hasColumn('schedules', 'destination_station_id')) {
                $table->dropForeign(['destination_station_id']);
                $table->dropColumn('destination_station_id');
            }
        });
    }
};
