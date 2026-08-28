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
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20);
            $table->string('time_slot', 5);
            $table->date('booking_date')->default(now()->toDateString());
            $table->string('reminder_sent')->default(false);
            $table->timestamps();

            $table->unique(['booking_date', 'time_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
