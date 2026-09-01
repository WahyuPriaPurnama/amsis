<?php

namespace App\Livewire\Hrd\Subsidiary;

use App\Models\HRD\Employee;
use App\Models\HRD\Subsidiary;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Daftar Perusahaan')]
class SubsidiaryIndex extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Toggle State Forms
    public $showCreateForm = false;
    public $showTransferForm = false;

    // Form Create fields
    public $name, $tagline, $npwp, $email, $phone, $address;
    public $logo, $logo_header, $logo_footer;

    // Form Transfer fields
    public $from_subsidiary_id = '';
    public $to_subsidiary_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->showTransferForm = false; // Sembunyikan form transfer
        $this->resetErrorBag();
        $this->reset(['name', 'tagline', 'npwp', 'email', 'phone', 'address', 'logo', 'logo_header', 'logo_footer']);
    }

    public function toggleTransferForm()
    {
        $this->showTransferForm = !$this->showTransferForm;
        $this->showCreateForm = false; // Sembunyikan form create
        $this->resetErrorBag();
        $this->reset(['from_subsidiary_id', 'to_subsidiary_id']);
    }

    public function save()
    {
        $validatedData = $this->validate([
            'name'        => 'required|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'npwp'        => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:255',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string',
            'logo'        => 'nullable|image|max:2048',
            'logo_header' => 'nullable|image|max:2048',
            'logo_footer' => 'nullable|image|max:2048',
        ]);

        if ($this->logo) {
            $path = $this->logo->store('public/subsidiary/logo');
            $validatedData['logo'] = basename($path);
        }

        if ($this->logo_header) {
            $pathHeader = $this->logo_header->store('public/subsidiary/logo_header');
            $validatedData['logo_header'] = basename($pathHeader);
        }

        if ($this->logo_footer) {
            $pathFooter = $this->logo_footer->store('public/subsidiary/logo_footer');
            $validatedData['logo_footer'] = basename($pathFooter);
        }

        Subsidiary::create($validatedData);

        session()->flash('success', 'Perusahaan berhasil ditambahkan.');
        $this->reset(['name', 'tagline', 'npwp', 'email', 'phone', 'address', 'logo', 'logo_header', 'logo_footer', 'showCreateForm']);
    }

    public function transferEmployees()
    {
        $this->validate([
            'from_subsidiary_id' => 'required|exists:subsidiaries,id',
            'to_subsidiary_id'   => 'required|exists:subsidiaries,id|different:from_subsidiary_id',
        ], [
            'to_subsidiary_id.different' => 'Plant tujuan tidak boleh sama dengan Plant asal.',
        ]);

        // Pindahkan seluruh karyawan dari plant asal ke plant tujuan
        $count = Employee::where('subsidiary_id', $this->from_subsidiary_id)
            ->update(['subsidiary_id' => $this->to_subsidiary_id]);

        session()->flash('success', "Sebanyak {$count} karyawan berhasil ditransfer.");
        $this->reset(['from_subsidiary_id', 'to_subsidiary_id', 'showTransferForm']);
    }

    public function render()
    {
        $allSubsidiaries = Subsidiary::orderBy('name', 'asc')->get();

        $subsidiaries = Subsidiary::withCount('employees')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%')
                    ->orWhere('npwp', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('hrd.subsidiary.subsidiary-index', [
            'subsidiaries'    => $subsidiaries,
            'allSubsidiaries' => $allSubsidiaries,
        ]);
    }
}
