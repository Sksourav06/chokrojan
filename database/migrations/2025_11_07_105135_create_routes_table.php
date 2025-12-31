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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();

            // Route Name (e.g., Chittagong-Dhaka)
            $table->string('name')->unique();

            // Foreign Key to Zone (from the Zone Management module)
            $table->foreignId('zone_id')
                ->constrained('zones')
                ->onDelete('restrict'); // Zone cannot be deleted if routes are linked

            // Status (active/inactive)
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};