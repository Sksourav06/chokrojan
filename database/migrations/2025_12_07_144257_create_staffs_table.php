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
        // ফিক্স: টেবিল ইতিমধ্যে থাকলে নতুন করে তৈরি করবে না
        if (!Schema::hasTable('staffs')) {
            Schema::create('staffs', function (Blueprint $table) {
                $table->id();
                $table->timestamps();

                // --- Core Personal Information ---
                $table->string('name', 100);
                $table->string('father_name', 100)->nullable();
                $table->string('date_of_birth')->nullable();
                $table->string('blood_group', 5)->nullable();
                $table->string('national_id', 20)->nullable()->unique();
                $table->text('present_address')->nullable();

                // --- Contact & Demographic Information ---
                $table->string('mobile_number', 15)->unique(); // গ্লোবাল ইউনিক নম্বর
                $table->string('alternative_contact_person', 100)->nullable();
                $table->string('alternative_mobile_number', 15)->nullable();
                $table->string('mother_name', 100)->nullable();
                $table->string('gender', 10);
                $table->string('religion', 20);
                $table->string('driving_license_number', 50)->nullable();
                $table->text('permanent_address')->nullable();

                // --- Job/Employment Details ---
                $table->unsignedBigInteger('staff_designation_id')->nullable();
                $table->string('job_type', 20);
                $table->date('joining_date')->nullable();
                $table->string('status', 20)->default('active');

                // Foreign Key Constraint
                $table->foreign('staff_designation_id')
                    ->references('id')
                    ->on('staff_designations')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};