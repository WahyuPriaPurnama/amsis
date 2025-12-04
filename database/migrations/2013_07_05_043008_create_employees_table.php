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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique()->index();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('divisi');
            $table->string('departemen');
            $table->string('seksi');
            $table->string('posisi');
            $table->string('status_peg');
            $table->date('tgl_masuk');
            $table->date('awal_kontrak')->nullable();
            $table->date('akhir_kontrak')->nullable();
            $table->string('nik')->unique()->nullable();
            $table->string('tmpt_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->string('pend_trkhr')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('thn_lulus')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('npwp')->nullable();
            $table->string('status')->nullable();
            $table->string('jml_ank')->nullable();
            $table->string('nama_kd')->nullable();
            $table->string('no_kd')->nullable();
            $table->string('hubungan')->nullable();
            $table->string('pp')->nullable();
            $table->foreignId('subsidiary_id')->constrained();
            $table->string('ktp')->nullable();
            $table->string('kk')->nullable();
            $table->string('npwp2')->nullable();
            $table->string('bpjs_ket')->nullable();
            $table->string('bpjs_kes')->nullable();
            $table->string('ttd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
