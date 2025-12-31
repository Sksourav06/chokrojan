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
            $table->unsignedBigInteger('issue_counter_id')->nullable()->after('issued_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_issues', function (Blueprint $table) {
            $table->dropColumn('issue_counter_id');
        });
    }
};
