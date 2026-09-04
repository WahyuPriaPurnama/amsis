<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReleaseNotes extends Component
{
    public $notes = [];
    public $isVisible = true;
    public $lastLoginTime;

    public function mount()
    {
        // 1. Data Release Notes (Bisa dipindah ke config atau database nantinya)
        $this->notes = [
            ['text' => 'bug fix pada fitur edit RO dan RFP', 'updated_at' => '2026-09-01 08:00:00'],
            ['text' => 'peningkatan performa, navigasi lebih responsive tanpa reload halaman.', 'updated_at' => '2026-09-01 09:30:00'],
            ['text'=>'sistem qr code untuk asset, scan qr code untuk menampilkan detail asset', 'updated_at'=>'2026-09-02 10:00:00'],
            ['text'=>'penambahan fitur ringkasan statistik asset, menampilkan jumlah asset berdasarkan nama barangnya', 'updated_at'=>'2026-09-02 11:00:00'],
        ];

        // 2. Cek apakah user sudah menutup (dismiss) notifikasi ini pada sesi aktif
        $this->isVisible = !session()->has('release_notes_dismissed');

        // 3. Ambil waktu login terakhir user. 
        // Asumsi: Anda punya kolom 'last_login_at' di tabel users. 
        // Jika belum punya, kita gunakan fallback tanggal 1 minggu yang lalu.
        $this->lastLoginTime = Auth::user()->last_login_at ?? Carbon::now()->subDays(7);
    }

    // Fungsi untuk menyembunyikan notifikasi saat tombol "Close" diklik
    public function dismiss()
    {
        $this->isVisible = false;
        session()->put('release_notes_dismissed', true);
    }

    public function render()
    {
        return view('livewire.release-notes');
    }
}
