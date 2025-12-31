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
        Schema::create('ticket_issue_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_issue_id')->constrained('ticket_issues')->onDelete('cascade');
            $table->foreignId('seat_sold_id')->nullable()->constrained('seat_solds')->nullOnDelete();
            $table->string('seat_number');
            $table->decimal('fare', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_issue_seats');
    }
};
