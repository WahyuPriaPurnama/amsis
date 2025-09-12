<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Employee;

class EmployeeAccountsSeeder extends Seeder
{
    public function run()
    {

        foreach (Employee::all() as $employee) {
            // Buat email dari nama
            $baseEmail = Str::slug($employee->nama, '.');
            $email = "{$baseEmail}";

            // Cek duplikat email
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = "{$baseEmail}{$counter}";
                $counter++;
            }


            // Buat user
            User::create([
                'name' => $employee->nama,
                'email' => $email,
                'password' => Hash::make('Karyawan_2025'),
                'employee_id' => $employee->id,
                'subsidiary_id' => $employee->subsidiary_id,
            ]);
        }
    }
}
