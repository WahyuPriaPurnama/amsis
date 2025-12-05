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
        Schema::create('request_orders', function (Blueprint $table) {
            $table->id();
            $table->string('division')->nullable();
            $table->date('request_date')->nullable();
            $table->string('request_number')->nullable();
            $table->text('purpose')->nullable();
            $table->string('status')->default('pending');

            // Signature workflow
            $table->unsignedBigInteger('requested_by');   // user id pengaju
            $table->unsignedBigInteger('approved_by_div_head')->nullable(); // kepala divisi
            $table->unsignedBigInteger('approved_by_manager')->nullable();  // plant manager
            $table->unsignedBigInteger('subsidiary_id')->nullable(); // subsidiary id

            $table->timestamps();
            $table->timestamp('approved_by_divhead_at')->nullable();
            $table->timestamp('approved_by_manager_at')->nullable();

            // Relasi ke tabel users
            $table->foreign('requested_by')->references('id')->on('users');
            $table->foreign('approved_by_div_head')->references('id')->on('users');
            $table->foreign('approved_by_manager')->references('id')->on('users');

            //relasi ke subsidiary
            $table->foreign('subsidiary_id')->references('id')->on('subsidiaries');
            $table->unique(['request_number', 'subsidiary_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_orders');
    }
};
