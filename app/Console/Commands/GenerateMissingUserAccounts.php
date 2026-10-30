<?php

namespace App\Console\Commands;

use App\Models\HRD\Employee;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GenerateMissingUserAccounts extends Command
{
    /**
     * Nama perintah yang dipanggil di terminal.
     */
    protected $signature = 'users:generate-missing';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Membuat akun User otomatis untuk semua Employee yang belum memiliki akun';

    public function handle()
    {
        // Cari employee yang ID-nya belum ada di kolom employee_id tabel users
        $employeesWithoutUser = Employee::whereDoesntHave('user')->get();

        if ($employeesWithoutUser->isEmpty()) {
            $this->info('Semua karyawan sudah memiliki akun User.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$employeesWithoutUser->count()} karyawan tanpa akun. Memulai pembuatan akun...");

        $count = 0;

        foreach ($employeesWithoutUser as $employee) {
            DB::transaction(function () use ($employee, &$count) {
                // Buat slug username dari nama karyawan
                $fullname = Str::slug($employee->nama, '.');
                $username = strtolower($fullname);
                $email = $username;

                // Mencegah email/username duplikat
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = "{$username}{$counter}";
                    $counter++;
                }

                // Buat User
                $user = User::create([
                    'name'          => $employee->nama,
                    'email'         => $email,
                    'password'      => Hash::make('Karyawan_2025'),
                    'employee_id'   => $employee->id,
                    'subsidiary_id' => $employee->subsidiary_id,
                ]);

                // Assign Role 'employee' jika menggunakan Spatie Permission
                if (method_exists($user, 'assignRole') && !$user->hasRole('employee')) {
                    $user->assignRole('employee');
                }

                $count++;
            });
        }

        $this->info("Berhasil membuat {$count} akun User baru.");
        return Command::SUCCESS;
    }
}
