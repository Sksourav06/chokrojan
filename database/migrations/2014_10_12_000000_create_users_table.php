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
        // Drop existing tables before creation for clean slate
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // --- Form Fields ---
            $table->string('mobile_number')->unique()->nullable();
            $table->string('username')->unique(); // Unique and non-nullable

            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Status: Matches 'active', 'inactive', 'blocked' options from the form
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');

            // Counter ID: Foreign Key (using modern constrained syntax)
            // $table->unsignedBigInteger('counter_id')->nullable();
            // --- End Form Fields ---

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations (Cleanup).
     */
    public function down(): void
    {
        // Drop sessions first
        Schema::dropIfExists('sessions');

        // Drop users table
        // When using Schema::dropIfExists('users'), Laravel removes constraints automatically.
        // However, if you were using Schema::table()->dropColumn(), you would need to drop the FK first.
        Schema::dropIfExists('users');
    }
};