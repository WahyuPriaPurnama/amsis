<?php

namespace App\Livewire\Hrd;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('E-Slip')]
class ESlipShow extends Component
{
    public string $plant = '';

    public function mount(string $plant)
    {
        $this->plant = strtolower($plant);
    }

    public function render()
    {
        // Daftar nama blade yang diizinkan di resources/views/hrd/e-slip/
        $validBlades = ['ams', 'bofi', 'eln1', 'eln2', 'haka', 'rmm'];

        $bladeName = in_array($this->plant, $validBlades) ? $this->plant : 'ams';

        // Langsung merender file Blade konvensional Anda yang sudah ada
        return view("hrd.e-slip.{$bladeName}")
            ->title('E-Slip ' . strtoupper($bladeName));
    }
}
