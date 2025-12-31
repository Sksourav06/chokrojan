<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_segment_solds', function (Blueprint $table) {
            $table->id();

            // সিটের মূল তথ্য
            $table->unsignedBigInteger('schedule_id'); // কোন ট্রিপের সিট
            $table->string('seat_number', 10);      // সিট নম্বর (e.g., A1, B2)
            $table->unsignedBigInteger('ticket_issue_id'); // কোন টিকেট এই সেগমেন্টটি কিনেছে

            // সেগমেন্টের তথ্য
            $table->unsignedBigInteger('from_station_id'); // কোথা থেকে শুরু
            $table->unsignedBigInteger('to_station_id');   // কোথায় শেষ

            // রুটের ক্রম (সিকোয়েন্স) -- Overlap Check এর জন্য Critical
            $table->integer('from_sequence');
            $table->integer('to_sequence');

            $table->timestamps();

            // ফরেন কি (Foreign Keys)
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('from_station_id')->references('id')->on('stations')->onDelete('cascade');
            $table->foreign('to_station_id')->references('id')->on('stations')->onDelete('cascade');

            // যাতে একটি সিট, একটি ট্রিপে এবং একটি সেগমেন্টে একবারই বিক্রি হতে পারে
            // যদিও সেগমেন্ট লজিকটি কোডের মাধ্যমে হ্যান্ডেল হবে, তবে এটি ডিফল্ট সুরক্ষা যোগ করে।
            // $table->unique(['schedule_id', 'seat_number', 'from_station_id', 'to_station_id']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_segment_solds');
    }
};