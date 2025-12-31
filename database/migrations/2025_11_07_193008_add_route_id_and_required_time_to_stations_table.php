<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->unsignedBigInteger('route_id')->nullable()->after('name');
            $table->string('required_time')->nullable()->after('route_id');
        });
    }

    public function down(): void
    {
        Schema::table('stations', function (Blueprint $table) {
            $table->dropColumn(['route_id', 'required_time']);
        });
    }
};
