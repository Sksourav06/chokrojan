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
        Schema::create('seat_locks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->string('seat_number', 5);
            $table->unsignedBigInteger('counter_id')->nullable(); // Which counter locked it
            $table->dateTime('expires_at'); // When the lock expires
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            // Add unique constraint to prevent double locking
            $table->unique(['schedule_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_locks');
    }
};
