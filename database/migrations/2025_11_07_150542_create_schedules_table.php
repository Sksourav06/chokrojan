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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // 🚨 ফিক্স ১: MasterSchedule এর সাথে যুক্ত করার জন্য Foreign Key যুক্ত করা হলো
            $table->foreignId('master_schedule_id')
                ->nullable()
                ->constrained('master_schedules')
                ->onDelete('cascade');

            // "Name" / Coach Info (e.g., 401). 
            // 🚨 ফিক্স ২: এটি অবশ্যই Unique থাকবে না, কারণ এটি প্রতিদিনের ট্রিপের কোড।
            $table->string('name')->index();

            // Foreign Key to the Buses table
            $table->foreignId('bus_id')
                ->constrained('buses')
                ->onDelete('restrict');

            // Foreign Key to the Routes table
            $table->foreignId('route_id')
                ->constrained('routes')
                ->onDelete('restrict');

            // Foreign Key to SeatLayouts
            $table->foreignId('seat_layout_id')
                ->constrained('seat_layouts')
                ->onDelete('restrict');

            // 🚨 ফিক্স ৩: start_time এবং end_time এখন DATETIME হবে 
            // (যাতে index ফাংশনে whereDate() কাজ করে)
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            // Route Tagline
            $table->string('route_tagline')->nullable();

            // Trip ends on the next day flag (আপনার পুরাতন কলাম)
            $table->boolean('start_time_nextday')->default(false);

            // Bus Type 
            $table->enum('bus_type', ['AC', 'Non AC', 'Sleeper']);

            // Status 
            $table->enum('status', ['active', 'inactive', 'hide'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};