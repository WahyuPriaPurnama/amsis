<?php

namespace App\Livewire\Auth;

use App\Helpers\LogActivity;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Login')]
class Login extends Component
{
    // Cukup definisikan rule-nya saja di atribut
    #[Validate('required|string')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    // Definisikan pesan kustom secara eksplisit per-field di sini
    public function messages()
    {
        return [
            'email.required' => 'Username tidak boleh kosong.',
            'email.string'   => 'Username harus berupa teks.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.string'   => 'Password harus berupa teks.',
        ];
    }

    public function authenticate()
    {
        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();

            LogActivity::addToLog('login berhasil', [
                'email' => $user->email,
                'subsidiary_id' => $user->subsidiary_id,
                'employee_id' => $user->employee_id,
            ]);

            $featureChanges = config('feature_changes.notes', []);
            if (!empty($featureChanges)) {
                session()->flash('feature_changes', $featureChanges);
            }

            session()->regenerate();

            return $this->redirect('/dashboard', navigate: true);
        }

        $this->addError('email', 'Username atau password yang Anda masukkan salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
