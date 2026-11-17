<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->date('booking_date');
            $table->string('booking_time')->nullable();
            $table->string('booking_source')->default('online');
            $table->text('symptoms')->nullable();
            $table->string('duration')->nullable();
            $table->string('severity')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->string('tariff_plan')->default('consultation_only');
            $table->decimal('total_amount', 12, 2)->default(180000.00);
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
