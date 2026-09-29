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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

   
            $table->string('company_name');
            $table->text('address');
            $table->string('nib')->unique();
            $table->string('npwp')->unique();

     
            $table->boolean('has_halal')->default(false);
            $table->boolean('has_haccp')->default(false);
            $table->string('skp_number')->nullable();

   
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');

    
            $table->string('pic_name');
            $table->string('pic_email')->unique();
            $table->string('pic_phone');

        
            $table->string('nib_file')->nullable();
            $table->string('npwp_file')->nullable();
            $table->string('certificate_file')->nullable();

       
            $table->string('contract_number')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('contract_file')->nullable();

            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
