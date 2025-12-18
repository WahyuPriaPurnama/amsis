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
        Schema::create('request_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number');
            $table->date('date');
            $table->string('division');
            $table->string('purpose');
            $table->decimal('grand_total', 15, 2);
            $table->string('status')->default('pending')->index();
            $table->string('attachment')->nullable();


            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by_manager')->nullable()->constrained('users');
            $table->foreignId('approved_by_bod')->nullable()->constrained('users');
            $table->foreignId('subsidiary_id')->constrained('subsidiaries');


            $table->timestamps();
            $table->timestamp('approved_by_manager_at')->nullable();
            $table->timestamp('approved_by_bod_at')->nullable();

            $table->unique(['payment_number', 'subsidiary_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_payments');
    }
};
