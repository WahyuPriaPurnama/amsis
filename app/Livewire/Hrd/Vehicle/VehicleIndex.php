<?php

namespace App\Livewire\Hrd\Vehicle;

use App\Models\HRD\Vehicle; // Sesuaikan dengan namespace Model Vehicle Anda
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Kendaraan')]
class VehicleIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $vehicles = Vehicle::with('subsidiary')
            ->when($this->search, function ($query) {
                $query->where('jenis_kendaraan', 'like', '%' . $this->search . '%')
                      ->orWhere('kategori', 'like', '%' . $this->search . '%')
                      ->orWhere('nopol', 'like', '%' . $this->search . '%')
                      ->orWhereHas('subsidiary', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy('jenis_kendaraan', 'asc')
            ->paginate(10);

        return view('hrd.vehicle.vehicle-index', [
            'vehicles' => $vehicles,
        ]);
    }
}