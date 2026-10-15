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
      
        $this->notes = [
            ['text'=>'sistem qr code untuk asset, scan qr code untuk menampilkan detail asset', 'updated_at'=>'2026-09-02 10:00:00'],
            ['text'=>'penambahan fitur ringkasan statistik asset, menampilkan jumlah asset berdasarkan nama barangnya', 'updated_at'=>'2026-09-02 11:00:00'],
        ];

   
        $this->isVisible = !session()->has('release_notes_dismissed');

        $this->lastLoginTime = Auth::user()->last_login_at ?? Carbon::now()->subDays(7);
    }

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
