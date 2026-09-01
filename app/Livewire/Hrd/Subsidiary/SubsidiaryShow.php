<?php

namespace App\Livewire\Hrd\Subsidiary;

use App\Models\HRD\Subsidiary;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class SubsidiaryShow extends Component
{
    use WithFileUploads;

    public Subsidiary $subsidiary;

    // State Form Inline Edit
    public $isEditing = false;

    // Form fields
    public $name;
    public $tagline;
    public $npwp;
    public $email;
    public $phone;
    public $address;
    public $new_logo;

    public function mount($subsidiary)
    {
        $this->subsidiary = $subsidiary instanceof Subsidiary
            ? $subsidiary
            : Subsidiary::with(['employees'])->findOrFail($subsidiary);

        $this->fillForm();
    }

    private function fillForm()
    {
        $this->name    = $this->subsidiary->name;
        $this->tagline = $this->subsidiary->tagline;
        $this->npwp    = $this->subsidiary->npwp;
        $this->email   = $this->subsidiary->email;
        $this->phone   = $this->subsidiary->phone;
        $this->address = $this->subsidiary->address;
    }

    public function toggleEditForm()
    {
        $this->isEditing = !$this->isEditing;

        if ($this->isEditing) {
            $this->fillForm();
            $this->reset(['new_logo']);
        }
        $this->resetErrorBag();
    }

    protected function rules()
    {
        return [
            'name'     => 'required|string|max:255',
            'tagline'  => 'nullable|string|max:255',
            'npwp'     => 'nullable|string|max:50',
            'email'    => 'nullable|email|max:255',
            'phone'    => 'nullable|string|max:50',
            'address'  => 'nullable|string',
            'new_logo' => 'nullable|image|max:2048',
        ];
    }

    public function update()
    {
        $validatedData = $this->validate();

        if ($this->new_logo) {
            // Hapus logo lama jika ada
            if ($this->subsidiary->logo) {
                Storage::delete('public/subsidiary/logo/' . $this->subsidiary->logo);
            }

            // Simpan logo baru
            $path = $this->new_logo->store('public/subsidiary/logo');
            $validatedData['logo'] = basename($path);
        }

        unset($validatedData['new_logo']);

        $this->subsidiary->update($validatedData);

        session()->flash('success', 'Data perusahaan berhasil diperbarui.');
        $this->isEditing = false;
    }

    public function deleteSubsidiary()
    {
        try {
            $this->subsidiary->delete();

            session()->flash('success', 'Perusahaan berhasil dihapus.');
            return $this->redirectRoute('subsidiaries.index', navigate: true);
        } catch (\Throwable $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            if ($errorCode == 23000 || str_contains($errorMessage, '23000') || str_contains($errorMessage, '1451')) {
                session()->flash('error', 'Perusahaan tidak dapat dihapus karena masih memiliki data karyawan terkait.');
                return;
            }

            session()->flash('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function render()
    {
        return view('hrd.subsidiary.subsidiary-show')
            ->title("Data " . $this->subsidiary->name);
    }
}
