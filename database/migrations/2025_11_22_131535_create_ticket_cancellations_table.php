<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_cancellations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id'); // reference to ticket_issues
            $table->unsignedBigInteger('cancelled_by'); // user who cancelled
            $table->timestamp('cancelled_at');
            $table->text('reason')->nullable(); // optional cancellation reason
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('ticket_issues')->onDelete('cascade');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_cancellations');
    }
};
