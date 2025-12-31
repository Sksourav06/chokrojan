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
        Schema::table('seat_solds', function (Blueprint $table) {
            if (Schema::hasColumn('seat_solds', 'seat_no')) {
                $table->dropColumn('seat_no');
            }
        });
    }

    public function down()
    {
        Schema::table('seat_solds', function (Blueprint $table) {
            $table->string('seat_no')->nullable();
        });
    }
};
