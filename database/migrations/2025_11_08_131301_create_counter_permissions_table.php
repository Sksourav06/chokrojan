<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCounterPermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('counter_permissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('counter_id');
            $table->unsignedBigInteger('station_id');
            $table->unsignedBigInteger('schedule_id');

            $table->boolean('can_sell')->default(true);
            $table->boolean('can_hold')->default(false);
            $table->boolean('can_block')->default(false);

            $table->timestamps();

            $table->foreign('counter_id')->references('id')->on('counters')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('counter_permissions');
    }
}
