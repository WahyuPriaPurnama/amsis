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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('cloud_id')->nullable();
            $table->string('pin');
            $table->dateTime('scan_date');
            $table->string('verify')->nullable();
            $table->string('status_scan')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
            $table->unique(['pin', 'scan_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
