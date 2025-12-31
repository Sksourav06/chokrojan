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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();

            // Registration details from the table columns
            $table->string('registration_number')->unique();
            $table->year('make_year')->nullable();
            $table->string('model_name')->nullable();
            $table->string('bus_type')->comment('e.g., Non AC, AC'); // For simplicity, leaving as string

            // Status (as seen in your image)
            $table->enum('status', ['running', 'maintenance', 'inactive'])->default('running');

            // ⭐ Foreign Key to the Seat Layouts Table ⭐
            // We assume the seat_layouts table (which holds the pattern config) already exists.
            $table->foreignId('seat_layout_id')
                ->constrained('seat_layouts')
                ->onDelete('restrict'); // Do not allow deleting a layout if a bus is using it

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};