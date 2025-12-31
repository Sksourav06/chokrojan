<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 🚨 ফিক্স: বিদ্যমান টেবিলে কলাম যুক্ত না করে, নতুন টেবিল তৈরি করা হচ্ছে।
        Schema::create('master_schedules', function (Blueprint $table) {
            $table->id();

            // ট্রিপের কোড (Trip Code)
            $table->string('trip_code')->unique();

            // রুট এবং বাসের তথ্য (Foreign Keys)
            $table->foreignId('bus_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');

            // স্টেশন তথ্য
            $table->foreignId('start_station_id')->constrained('stations')->onDelete('cascade');
            $table->foreignId('end_station_id')->constrained('stations')->onDelete('cascade');

            // শুধুমাত্র সময়
            $table->time('start_time_only');
            $table->time('end_time_only');

            // অন্যান্য তথ্য
            $table->string('bus_type')->default('AC');
            $table->boolean('start_time_nextday')->default(false)->comment('1 if trip ends on the next day');
            $table->string('status')->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        // ফিক্স: Schema::table এর পরিবর্তে Schema::dropIfExists ব্যবহার করা হলো
        Schema::dropIfExists('master_schedules');
    }
};