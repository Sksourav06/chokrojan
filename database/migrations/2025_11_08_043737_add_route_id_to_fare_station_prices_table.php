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
        Schema::table('fare_station_prices', function (Blueprint $table) {
            $table->unsignedBigInteger('route_id')->after('fare_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fare_station_prices', function (Blueprint $table) {
            $table->dropColumn('route_id');
        });
    }
};
