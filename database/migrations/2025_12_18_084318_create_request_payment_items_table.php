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
        Schema::create('request_payment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_payment_id')
                ->constrained('request_payments')
                ->onDelete('cascade');
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->string('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_payment_items');
    }
};
