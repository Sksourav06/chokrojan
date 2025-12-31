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
        Schema::create('loyalty_discounts', function (Blueprint $table) {
            $table->id();
            // কাস্টমার পুনরায় টিকিট কাটার জন্য সর্বোচ্চ দিন সংখ্যা
            $table->integer('days_threshold')->unique()->comment('Days within last purchase for discount eligibility');
            // এই রুলস অনুযায়ী কত টাকা ডিসকাউন্ট দেওয়া হবে
            $table->decimal('discount_amount', 8, 2)->comment('Discount amount in local currency');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_discounts');
    }
};
