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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_name');
            $table->decimal('min_fare', 10, 2);   // সর্বনিম্ন ভাড়া (যেমন ১৬০০)
            $table->decimal('max_fare', 10, 2);   // সর্বোচ্চ ভাড়া (যেমন ৫০০০)
            $table->decimal('discount_amount', 10, 2); // ডিসকাউন্ট (যেমন ২০০)
            $table->date('start_date');           // অফার শুরুর তারিখ
            $table->date('end_date');             // অফার শেষ হওয়ার তারিখ
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
