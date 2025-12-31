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
        Schema::table('counters', function (Blueprint $table) {
            $table->time('from_time')->nullable()->after('name');
            $table->time('to_time')->nullable()->after('from_time');
        });
    }

    public function down()
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->dropColumn(['from_time', 'to_time']);
        });
    }
};
