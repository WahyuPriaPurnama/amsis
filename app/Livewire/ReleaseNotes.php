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
            ['text'=>'absensi untuk plant BOFI 2 Jember dapat ditarik melalui sistem', 'updated_at'=>'2026-10-28 14:37:00'],
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
