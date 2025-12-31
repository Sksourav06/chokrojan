<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ticket_issues', function (Blueprint $table) {

            // Route Info
            $table->unsignedBigInteger('from_station_id')->nullable()->after('schedule_id');
            $table->unsignedBigInteger('to_station_id')->nullable()->after('from_station_id');

            // Seat Details
            $table->string('seat_numbers')->nullable()->after('customer_mobile'); // Example: "A1,A2,A3"
            $table->integer('seats_count')->default(0)->after('seat_numbers');

            // Fare
            $table->decimal('fare', 10, 2)->nullable()->after('sub_total');

            // Goods Charge + Callerman Commission
            $table->decimal('goods_charge', 10, 2)->default(0)->after('service_charge');
            $table->decimal('callerman_commission', 10, 2)->default(0)->after('goods_charge');

            // Issued By
            $table->unsignedBigInteger('issued_by')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropColumn([
                'from_station_id',
                'to_station_id',
                'seat_numbers',
                'seats_count',
                'fare',
                'goods_charge',
                'callerman_commission',
                'issued_by',
            ]);
        });
    }

};
