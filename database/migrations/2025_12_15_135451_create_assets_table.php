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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('accounting_code')->nullable();
            $table->enum('category', ['Tanah & Bangunan', 'Mesin', 'Furniture & Fixture', 'Kendaraan', 'Alat Kerja']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('owner', ['General', 'Engineering', 'QC & Lab']);
            $table->string('photo')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('unit');
            $table->date('usage_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->enum('condition', ['Baik', 'Rusak', 'Hilang'])->default('Baik');
            $table->decimal('purchase_value', 15, 2)->default(0);
            $table->decimal('depreciation_value', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->integer('useful_life')->default(0); // in months
            $table->string('attachment')->nullable();

            $table->foreignId('subsidiary_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            $table->unique(['code', 'subsidiary_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
