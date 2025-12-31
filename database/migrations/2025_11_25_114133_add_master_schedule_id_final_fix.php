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
        // এই লজিকটি শুধুমাত্র তখনই চালান, যখন কলামটি বিদ্যমান না থাকে।
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'master_schedule_id')) {
                $table->foreignId('master_schedule_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('master_schedules')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'master_schedule_id')) {
                $table->dropForeign(['master_schedule_id']);
                $table->dropColumn('master_schedule_id');
            }
        });
    }
};