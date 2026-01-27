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
        Schema::create('request_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_order_id')
                ->constrained('request_orders')
                ->onDelete('cascade');
            $table->string('item_name');
            $table->unsignedInteger('quantity');
            $table->string('unit');
            $table->string('remark')->nullable();
            $table->date('date_received')->nullable();
            $table->unsignedInteger('qty_received')->default(0);
            $table->string('receipt_attachment')->nullable();
            $table->date('po_date')->nullable();
            $table->string('po_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
