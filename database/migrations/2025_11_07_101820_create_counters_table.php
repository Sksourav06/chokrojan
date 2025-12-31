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
        Schema::create('counters', function (Blueprint $table) {
            $table->id();

            // ⭐ 1. Station (Foreign Key to Stations table) ⭐
            // এটি নির্দেশ করে যে কোন স্টেশনের অধীনে এই কাউন্টারটি রয়েছে
            $table->foreignId('station_id')
                ->constrained('stations')
                ->onDelete('restrict');

            // 2. Counter Name
            $table->string('name');

            // 3. Type (e.g., Own/Commission)
            $table->enum('counter_type', ['Own', 'Commission']);

            // 5, 6, 7. Credit fields (using decimal for financial data)
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->decimal('credit_balance', 10, 2)->default(0);
            // Permitted Credit field is present in your list, though its purpose is similar to limit/balance
            $table->decimal('permitted_credit', 10, 2)->default(0);

            // 8. Status (e.g., active/inactive)
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Add a unique index for (station_id and name) to ensure no duplicate counter names per station
            $table->unique(['station_id', 'name']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};