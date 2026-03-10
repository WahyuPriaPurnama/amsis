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
        Schema::create('counters', function (Blueprint $table) {
            $table->id();
            // Menggunakan string untuk nama seamer (Seamer1, Seamer2, Seamer3)
            $table->string('seamer_name')->index();

            $table->unsignedInteger('rpm')->default(0);

            // Menggunakan BigInteger karena angka counter produksi bisa sangat besar
            $table->unsignedBigInteger('counter')->default(0);

            $table->string('device_id')->nullable();
            $table->string('location')->nullable();

            // Tambahkan index pada timestamps untuk mempercepat query grafik/history
            $table->timestamps();
            $table->index('created_at');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
