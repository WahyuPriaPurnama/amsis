<?php

namespace App\Livewire\Purchasing;

use App\Models\Purchasing\RequestPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Detail Request Payment')]
class RequestPaymentShow extends Component
{
    public RequestPayment $payment;

    public function mount(RequestPayment $requestPayment)
    {
        $this->payment = $requestPayment->load([
            'subsidiary',
            'items',
            'requester',
            'plantManager',
            'bod'
        ]);
    }

    public function deletePayment()
    {
        $user = auth()->user();
        if (!($user->can('request-payment.delete') || $user->hasRole('super-admin'))) {
            session()->flash('error', 'Anda tidak memiliki hak akses untuk menghapus dokumen ini.');
            return;
        }

        DB::beginTransaction();
        try {
            if ($this->payment->attachment && Storage::disk('public')->exists($this->payment->attachment)) {
                Storage::disk('public')->delete($this->payment->attachment);
            }

            foreach ($this->payment->items as $item) {
                if (isset($item->attachment) && $item->attachment && Storage::disk('public')->exists($item->attachment)) {
                    Storage::disk('public')->delete($item->attachment);
                }
            }

            $this->payment->items()->delete();
            $this->payment->delete();

            DB::commit();

            session()->flash('success', 'Request Payment berhasil dihapus!');
            
            // Menggunakan pengalihan resmi Livewire 3
            return $this->redirectRoute('request-payment.index', navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    public function approveManager()
    {
        $user = auth()->user();
        if (!$user->can('request-payment.approve-manager') && !$user->hasRole('super-admin')) {
            session()->flash('error', 'Aksi tidak diizinkan.');
            return;
        }

        $this->payment->update([
            'status'                 => 'approved_by_manager',
            'approved_by_manager_at' => now(),
            'approved_by_manager' => auth()->id(),
        ]);

        session()->flash('success', 'Request Payment berhasil disetujui Direktur.');
        $this->payment->refresh();
    }

    public function approveBod()
    {
        $user = auth()->user();
        if (!$user->can('request-payment.approve-bod') && !$user->hasRole('super-admin')) {
            session()->flash('error', 'Aksi tidak diizinkan.');
            return;
        }

        $this->payment->update([
            'status'             => 'approved_by_bod',
            'approved_by_bod_at' => now(),
            'approved_by_bod' => auth()->id(),
        ]);

        session()->flash('success', 'Request Payment berhasil disetujui Direktur Operasional.');
        $this->payment->refresh();
    }

    public function unapprove()
    {
        if ($this->payment->status === 'approved_by_bod') {
            $this->payment->update([
                'status'             => 'approved_by_manager',
                'approved_by_bod_at' => null,
                'approved_by_bod' => null,
            ]);
        } elseif ($this->payment->status === 'approved_by_manager') {
            $this->payment->update([
                'status'                 => 'pending',
                'approved_by_manager_at' => null,
                'approved_by_manager' => null,
            ]);
        }

        session()->flash('success', 'Persetujuan berhasil dibatalkan.');
        $this->payment->refresh();
    }

    public function render()
    {
        return view('purchasing.request-payment.show');
    }
}