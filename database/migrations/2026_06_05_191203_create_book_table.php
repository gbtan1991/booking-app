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
        Schema::create('book', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->date('date')->index();           // indexed — queried heavily
            $table->string('time', 10);              // "09:00" format
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_telephone', 30);
            $table->text('customer_notes')->nullable();
            $table->string('status', 20)->default('current'); // current | completed | cancelled

            $table->timestamps();

            // Composite index for slot-availability queries
            $table->index(['date', 'time', 'status'], 'idx_book_date_time_status');
            // Admin filter by status
            $table->index(['status', 'date'], 'idx_book_status_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
