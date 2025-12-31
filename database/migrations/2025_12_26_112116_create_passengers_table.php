<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            // কোন টিকেটের সাথে যুক্ত (Foreign Key)
            $table->unsignedBigInteger('ticket_issue_id');

            // যাত্রীর বিস্তারিত তথ্য
            $table->string('name');
            $table->string('mobile_number')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('seat_number'); // যেমন: A1, B2
            $table->decimal('fare', 10, 2); // টিকেটের মূল্য

            $table->timestamps();

            // টিকেট টেবিলের সাথে রিলেশন সেট করা
            $table->foreign('ticket_issue_id')->references('id')->on('ticket_issues')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};