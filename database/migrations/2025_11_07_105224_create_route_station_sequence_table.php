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
        Schema::create('route_station_sequence', function (Blueprint $table) {

            // Foreign Key to the Routes table
            $table->foreignId('route_id')
                ->constrained('routes')
                ->onDelete('cascade');

            // Foreign Key to the Stations table
            $table->foreignId('station_id')
                ->constrained('stations')
                ->onDelete('cascade');

            // ⭐ Sequence Order (Required to know which station comes first/second/etc.) ⭐
            $table->integer('sequence_order')->default(0);

            // Composite Primary Key (ensures no duplicate route/station combination)
            $table->primary(['route_id', 'station_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_station_sequence');
    }
};