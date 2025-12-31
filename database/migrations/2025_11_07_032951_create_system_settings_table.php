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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            // COMMON SETTINGS
            $table->string('site_name')->nullable();
            $table->string('logo')->nullable();
            $table->integer('booking_cancel_time')->default(0);
            $table->integer('permitted_seat_block_release_time')->default(0);
            $table->integer('advance_booking')->default(0);
            $table->integer('selected_seat_lifetime')->default(0);
            $table->boolean('seat_cancel_allow')->default(false);
            $table->integer('previous_date_view_allow')->default(0);
            $table->integer('passenger_star_rating')->default(0);
            $table->boolean('booking')->default(true);
            $table->boolean('vip_booking')->default(false);
            $table->boolean('goods_charge')->default(false);
            $table->boolean('callerman_commission')->default(false);
            $table->boolean('discount')->default(false);
            $table->boolean('discount_show_in_ticket')->default(false);

            // COUNTER SETTINGS
            $table->integer('booking_lifetime')->default(0);
            $table->integer('counter_sales_allow_time')->default(0);
            $table->integer('counter_max_seat_per_ticket')->default(0);
            $table->boolean('counter_cancel_allow')->default(false);
            $table->decimal('counter_cancel_fine', 5, 2)->default(0);
            $table->integer('counter_cancel_allow_time')->default(0);

            // ONLINE SETTINGS
            $table->integer('online_booking_lifetime')->default(0);
            $table->integer('online_sales_disallow_time')->default(0);
            $table->integer('online_max_seat_per_ticket')->default(0);
            $table->boolean('online_cancel_allow')->default(false);
            $table->decimal('online_cancel_fine', 5, 2)->default(0);
            $table->integer('online_cancel_allow_time')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
