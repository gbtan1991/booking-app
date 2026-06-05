<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->date('booking_date');
            $table->string('time_slot', 10); // "09:00" format
            $table->string('service')->default('General Consultation');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('pending');
            $table->softDeletes();
            $table->timestamps();

            // Composite index for slot availability queries
            $table->index(['booking_date', 'time_slot', 'status'], 'idx_date_slot_status');
            // Admin listing: filter by status then sort by date
            $table->index(['status', 'booking_date'], 'idx_status_date');
            // Client email lookup
            $table->index('email', 'idx_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
