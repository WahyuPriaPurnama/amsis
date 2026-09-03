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
            $table->text('description')->nullable();
            $table->text('name');
            $table->enum('condition', ['Baik', 'Rusak', 'Lainnya'])->default('Baik');
            $table->enum('owner', ['Umum', 'Engineering', 'QC & Lab']);
            $table->string('location');
            $table->enum('category', [
                'Tanah & Bangunan',
                'Mesin',
                'Furniture & Fixture',
                'Kendaraan',
                'Alat Kerja',
                'Fasilitas'
            ]);
            $table->string('delivery_receipt')->nullable();
            $table->string('manual_book')->nullable();
            $table->string('accounting_code')->nullable();
            $table->string('photo')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('unit');
            $table->date('usage_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_value', 15, 2)->default(0.00);
            $table->decimal('depreciation_value', 15, 2)->default(0.00);
            $table->decimal('total_value', 15, 2)->default(0.00);
            $table->integer('useful_life')->default(0);
            $table->string('attachment')->nullable();

            // Kolom QR Code tambahan
            $table->string('qr_code')->nullable();

            // Foreign Keys
            $table->foreignId('subsidiary_id')->constrained('subsidiaries')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();

            // Unique constraint gabungan kode dan subsidiary sesuai definisi SQL
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
