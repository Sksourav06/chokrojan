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
        Schema::create('fare_station_prices', function (Blueprint $table) {
            $table->id();

            // কোন Fare Rule-এর জন্য এই ভাড়া?
            $table->foreignId('fare_id')
                ->constrained('fares')
                ->onDelete('cascade');

            // কোন স্টেশন থেকে? (e.g., Dhaka)
            $table->foreignId('origin_station_id')
                ->constrained('stations')
                ->onDelete('cascade');

            // কোন স্টেশনে? (e.g., Chittagong)
            $table->foreignId('destination_station_id')
                ->constrained('stations')
                ->onDelete('cascade');

            // ভাড়া কত?
            $table->decimal('price', 8, 2);

            // Ensure a unique fare for each station pair within a rule
            $table->unique(['fare_id', 'origin_station_id', 'destination_station_id'], 'fare_station_pair_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_station_prices');
    }
};