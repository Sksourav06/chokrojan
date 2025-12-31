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
        Schema::create('seat_layouts', function (Blueprint $table) {
            $table->id();

            // 1. Descriptive Fields (From H5 tag and labels)
            $table->string('name')->nullable()->comment('e.g., 2 + 2 X 9 = 37');
            $table->unsignedSmallInteger('total_seats');

            // 2. Structural Fields
            $table->unsignedTinyInteger('rows')->comment('Total number of rows');
            $table->unsignedTinyInteger('columns')->comment('Seats per row (e.g., 4 for 2+2)');

            // Deck and Class Information (Using ENUMs based on provided data)
            $table->enum('deck_type', ['Single Deck', 'Double Deck'])->default('Single Deck');

            // Since multiple classes can exist, JSON is best for storage efficiency
            $table->json('class_types')->comment('e.g., ["Economy Class", "Business Class"]');

            // 3. Seat Map Configuration (The core data for rendering the seat grid)
            // Storing the abstract representation of the seat grid (where is the aisle, window, blank space, etc.)
            $table->json('seat_map_config')->comment('JSON representation of the seat grid layout');

            // Status and Timestamps
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_layouts');
    }
};