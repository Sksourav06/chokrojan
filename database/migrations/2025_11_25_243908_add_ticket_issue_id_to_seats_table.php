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
        Schema::table('seats', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_issue_id')->nullable()->after('schedule_id');
            $table->foreign('ticket_issue_id')->references('id')->on('ticket_issues')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign(['ticket_issue_id']);
            $table->dropColumn('ticket_issue_id');
        });
    }
};
