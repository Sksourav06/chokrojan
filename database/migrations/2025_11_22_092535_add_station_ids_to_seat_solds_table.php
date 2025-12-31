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
            $table->foreignId('from_station_id')->nullable()->after('seat_number')->constrained('stations');
            $table->foreignId('to_station_id')->nullable()->after('from_station_id')->constrained('stations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_solds', function (Blueprint $table) {
            $table->dropForeign(['from_station_id']);
            $table->dropForeign(['to_station_id']);
            $table->dropColumn(['from_station_id', 'to_station_id']);
        });
    }
};
