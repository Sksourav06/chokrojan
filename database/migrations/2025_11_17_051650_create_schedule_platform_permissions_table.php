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
        Schema::create('schedule_platform_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('platform_id');

            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();

            $table->json('blocked_seats')->nullable(); // ["A1", "A2"]

            $table->boolean('status')->default(1); // active/inactive

            $table->timestamps();

            // Foreign keys
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_platform_permissions');
    }
};
