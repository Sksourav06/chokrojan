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
        Schema::table('ticket_issues', function (Blueprint $table) {
            //// 🚨 নতুন কলাম ১: কাউন্টার কমিশন পরিমাণ
            // এটি সেলের সময় হিসাব করা কমিশনের চূড়ান্ত টাকা রাখবে।
            $table->decimal('counter_commission_amount', 8, 2)
                ->default(0.00)
                ->nullable()
                ->after('callerman_commission');

            // 🚨 নতুন কলাম ২: যাত্রার তারিখ
            // এটি 'journey_date' কলাম যোগ করবে, যা রিপোর্টিং-এর জন্য প্রয়োজন।
            $table->date('journey_date')
                ->nullable()
                ->after('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropColumn('counter_commission_amount');
            $table->dropColumn('journey_date');
        });
    }
};
