<?php

namespace App\Livewire\Hrd;

use App\Models\HRD\Employee;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Data Karyawan')]
class EmployeeIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $employees = Employee::with('subsidiary')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('hrd.employee.index', [
            'employees' => $employees,
        ]);
    }
}
