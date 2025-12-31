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
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('trip_id')->nullable()->after('offer_name');
            $table->unsignedBigInteger('route_id')->nullable()->after('offer_name');
            $table->string('bus_type')->nullable()->after('route_id'); // e.g., 'AC', 'Non-AC'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            //
        });
    }
};
