<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            // বিদ্যমান টেবিলের সাথে এই নতুন ফিল্ডগুলো যুক্ত হবে
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name')->nullable();
            $table->string('email')->after('mobile_number')->nullable()->unique();
            $table->string('password')->after('email')->nullable(); // Change Password এর জন্য

            // ঠিকানা সংক্রান্ত তথ্যসমূহ
            $table->text('street_address')->after('password')->nullable();
            $table->string('city')->after('street_address')->nullable();
            $table->string('zip_code')->after('city')->nullable();

            // জেন্ডার কলামটি আগে না থাকলে যোগ করুন
            if (!Schema::hasColumn('passengers', 'gender')) {
                $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->default('MALE')->after('last_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            // রোলব্যাক করার সময় কলামগুলো মুছে ফেলার জন্য
            $table->dropColumn(['first_name', 'last_name', 'email', 'password', 'street_address', 'city', 'zip_code', 'gender']);
        });
    }
};