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
        Schema::table('loyalty_discounts', function (Blueprint $table) {
            if (!Schema::hasColumn('loyalty_discounts', 'start_date')) {
                $table->date('start_date')->after('discount_amount')->nullable();
            }
            if (!Schema::hasColumn('loyalty_discounts', 'end_date')) {
                $table->date('end_date')->after('start_date')->nullable();
            }
            if (!Schema::hasColumn('loyalty_discounts', 'is_active')) {
                $table->tinyInteger('is_active')->after('end_date')->default(1);
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_discounts', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'is_active']);
        });
    }
};
