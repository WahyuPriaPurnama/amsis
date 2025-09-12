<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Subsidiary;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;



class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Subsidiary::create([
        //     'name' => 'CV. AMS',
        //     'tagline' => 'General Trading and Supplier',
        //     'npwp' => '70.651.906.3-657.000',
        //     'email' => 'headoffice@amsgroup.co.id',
        //     'phone' => '0341 5054969',
        //     'address' => 'Perum P. Pratama B2/1P, Karangploso, Malang - East Java 65152, Indonesia'
        // ]);
        // Subsidiary::create([
        //     'name' => 'PT. ELN 1',
        //     'tagline' => 'Can Maker',
        //     'npwp' => '94.024.473.4-657.000',
        //     'email' => 'headoffice@eln.co.id',
        //     'phone' => '0341 5058269',
        //     'address' => 'Dusun Boro Nggondang, RT 52 / RW 13, Tawangargo Karangploso, Malang
        //                     East Java 65152'
        // ]);
        // Subsidiary::create([
        //     'name' => 'PT. ELN 2',
        //     'tagline' => 'ABF and Cold Storage',
        //     'npwp' => '94.024.473.4-657.000',
        //     'email' => 'headoffice@eln.co.id',
        //     'phone' => '0333 424150',
        //     'address' => 'Jalan Bawean No. 7, Klatak, Kalipuro, Banyuwangi
        //                     East Java 68421'
        // ]);
        // Subsidiary::create([
        //     'name' => 'PT. BOFI',
        //     'tagline' => 'Canning',
        //     'npwp' => '43.623.790.3-625.000',
        //     'email' => 'headoffice@blueoceanfoods.co.id',
        //     'phone' => '0333 2815013',
        //     'address' => 'Dusun Sampangan, Kedungrejo, Muncar, Banyuwangi Regency, East Java'
        // ]);
        // Subsidiary::create([
        //     'name' => 'PT. HAKA',
        //     'tagline' => 'Open Trader With Us',
        //     'npwp' => '43.623.790.3-625.000',
        //     'email' => 'hkcipta@blueoceanfoods.co.id',
        //     'phone' => '0333 2815013',
        //     'address' => 'Kalipuro'
        // ]);

        // Subsidiary::create([
        //     'name' => 'PT. RMM',
        //     'tagline' => 'Canning',
        //     'npwp' => '43.623.790.3-625.000',
        //     'email' => 'headoffice@blueoceanfoods.co.id',
        //     'phone' => '0333 2815013',
        //     'address' => ' Dusun Sampangan, Kedungrejo, Muncar, Banyuwangi Regency, East Java'
        // ]);


        // for ($i = 0; $i < 100; $i++) {
        //     Employee::create([
        //         'nip' => $faker->randomNumber(9),
        //         'nama' => $faker->name(),
        //         'nik' => $faker->nik(),
        //         'subsidiary_id' => $faker->numberBetween(1, 6),
        //         'divisi' => $faker->randomElement(['IT', 'HRD', 'Teknik', 'Produksi']),
        //         'departemen' => $faker->randomElement(['IT', 'HRD', 'Teknik', 'Produksi']),
        //         'seksi' => $faker->randomElement(['IT', 'HRD', 'Teknik', 'Produksi']),
        //         'posisi' => $faker->randomElement(['Manager', 'Supervisor', 'Staff', 'Operator']),
        //         'status_peg' => $faker->randomElement(['PKWT', 'PKWTT']),
        //         'tgl_masuk' => $faker->date(),
        //         'awal_kontrak' => $faker->dateTimeBetween('2019-01-01', '2022-01-01'),
        //         'akhir_kontrak' => $faker->dateTimeBetween('2024-11-11', '2025-01-01'),

        //         'tmpt_lahir' => $faker->city(),
        //         'tgl_lahir' => $faker->date(),
        //         'jenis_kelamin' => $faker->randomElement(['L', 'P']),
        //         'alamat' => $faker->address(),
        //         'no_telp' => $faker->numerify('085#########'),
        //         'email' => $faker->email(),
        //         'pend_trkhr' => $faker->randomElement(['Diploma', 'Sarjana', 'Magister', 'Doktor']),
        //         'jurusan' => $faker->randomElement(['Teknik Komputer', 'Ekonomi', 'Akutansi', 'Teknik Sipil']),
        //         'thn_lulus' => $faker->randomElement(['2020', '2014', '2015', '2016']),
        //         'nama_ibu' => $faker->name(),
        //         'npwp' => '61.314.708.1-625.000',
        //         'status' => $faker->randomElement(['Kawin', 'Belum Kawin']),
        //         'jml_ank' => $faker->randomDigit(),
        //         'nama_kd' => $faker->name(),
        //         'no_kd' => $faker->numerify('085#########'),
        //         'hubungan' => $faker->randomElement(['Saudara Kandung', 'Saudara Sepupu', 'Ipar'])
        //     ]);

        //     Vehicle::create([
        //         'jenis_kendaraan' => $faker->randomElement(['Toyota Starlet', 'Toyota Avanza', 'Toyota Veloz', 'Toyota Fortuner', 'Honda Jazz']),
        //         'kategori' => $faker->randomElement(['Pribadi', 'Kantor', 'Umum']),
        //         'subsidiary_id' => $faker->numberBetween(1, 6),
        //         'tgl_perolehan' => $faker->date(),
        //         'pengguna' => $faker->name(),
        //         'nama_warna' => $faker->colorName(),
        //         'warna' => $faker->hexColor(),
        //         'tahun' => $faker->year(),
        //         'atas_nama' => $faker->name(),
        //         'nopol' => $faker->numerify('# #### ##'),
        //         'no_rangka' => $faker->randomNumber(9),
        //         'no_bpkb' => $faker->randomNumber(9),
        //         'no_mesin' => $faker->randomNumber(9),
        //         'stnk' => $faker->date(),
        //         'pajak' => $faker->date(),
        //         'kir' => $faker->date(),
        //         'jth_tempo' => $faker->date(),
        //         'kondisi' => $faker->randomElement(['Baik', 'Kurang Baik'])
        //     ]);


        $admins = [
            ['name' => 'Super Admin', 'email' => 'super.admin@amsgroup.co.id', 'password' => 'SuperAdmin_1996'],
            ['name' => 'Holding Admin', 'email' => 'holding.admin@amsgroup.co.id', 'password' => 'HoldingAdmin_9H!7'],
            ['name' => 'ELN Admin', 'email' => 'eln.admin@amsgroup.co.id', 'password' => 'ELNAdmin_6c\9'],
            ['name' => 'ELN 2 Admin', 'email' => 'eln2.admin@amsgroup.co.id', 'password' => 'ELN2Admin_tT45'],
            ['name' => 'Haka Admin', 'email' => 'haka.admin@amsgroup.co.id', 'password' => 'HakaAdmin_a6^0'],
            ['name' => 'BOFI Admin', 'email' => 'bofi.admin@amsgroup.co.id', 'password' => 'BOFIAdmin_50U('],
            ['name' => 'RMM Admin', 'email' => 'rmm.admin@amsgroup.co.id', 'password' => 'RMMAdmin_177v'],
        ];

        foreach ($admins as $admin) {
            if (!User::where('email', $admin['email'])->exists()) {
                User::create([
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => Hash::make($admin['password']),
                ]);
            }
        }
    }
}
