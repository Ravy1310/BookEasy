<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->index('booking_date');
            $table->index('time_slot');
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::table('booking', function (Blueprint $table) {
            $table->dropIndex(['booking_date']);
            $table->dropIndex(['time_slot']);
        });

        Schema::table('holidays', function (Blueprint $table) {
            $table->dropIndex(['date']);
        });
    }
};
