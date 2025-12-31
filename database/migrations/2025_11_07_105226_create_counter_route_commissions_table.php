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
        Schema::create('counter_route_commissions', function (Blueprint $table) {

            $table->id(); // add an auto primary key for clarity

            // Foreign key to counters table
            $table->unsignedBigInteger('counter_id');
            $table->unsignedBigInteger('route_id');

            $table->decimal('ac_commission', 8, 2)->default(0);
            $table->decimal('non_ac_commission', 8, 2)->default(0);

            $table->timestamps();


            $table->foreign('counter_id')
                ->references('id')
                ->on('counters')
                ->onDelete('cascade');

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');

            // ✅ Optionally ensure uniqueness for each (counter, route)
            $table->unique(['counter_id', 'route_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counter_route_commissions');
    }
};
