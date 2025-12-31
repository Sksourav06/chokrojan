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
        Schema::create('fares', function (Blueprint $table) {
            $table->id();

            // "Name" column (e.g., "Ctg-Dhk", "Bpl-37")
            $table->string('name')->unique();

            // "Route" column (links to routes table)
            $table->foreignId('route_id')
                ->constrained('routes')
                ->onDelete('cascade');

            // "Bus Type" column
            $table->enum('bus_type', ['AC', 'Non AC']);

            // "Seat Plan" column (links to seat_layouts table)
            $table->foreignId('seat_layout_id')
                ->constrained('seat_layouts')
                ->onDelete('restrict');

            // "Date Period" columns
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // "Status" column
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fares');
    }
};