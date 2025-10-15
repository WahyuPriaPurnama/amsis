<?php

namespace App\Console\Commands;

use App\Mail\MyTestMail;
use App\Models\Employee;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send mail reminder to HRD & GA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('m-d');
        $birthdayEmployees = Employee::with('subsidiary')
            ->whereRaw("DATE_FORMAT(tgl_lahir, '%m-%d') = ?", [$today])
            ->get();

        foreach ($birthdayEmployees as $employee) {
            $subsidiaryName = optional($employee->subsidiary)->name ?? 'Tidak diketahui';

            $mailData = [
                'type' => 'birthday',
                'title' => 'Selamat Ulang Tahun ' . $employee->nama,
                'body' => "Kami dari HRD AMS Group mengucapkan selamat ulang tahun kepada " . $employee->nama . " dari plant " . $subsidiaryName . ". Semoga sehat dan sukses selalu!"
            ];

            $toEmail = null;
            $bccEmails = ['it@amsgroup.co.id'];

            $email = trim($employee->email);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $toEmail = $email;
            } else {
                Log::warning("Email tidak valid: {$employee->nama} - '{$employee->email}'");
            }

            if ($toEmail) {
                try {
                    Mail::to($toEmail)
                        ->bcc($bccEmails)
                        ->queue(new MyTestMail($mailData, $employee));

                    Log::info('Email ulang tahun dikirim ke:', [
                        'email' => $toEmail,
                        'nama' => $employee->nama,
                        'subsidiary' => $subsidiaryName,
                    ]);
                } catch (\Throwable $e) {
                    Log::error("Gagal kirim email ulang tahun untuk {$employee->nama}: " . $e->getMessage(), [
                        'email' => $toEmail,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }

        $targetDates = [
            Carbon::now()->addDays(30)->toDateString(),
            Carbon::now()->addDays(15)->toDateString(),
            Carbon::now()->addDays(7)->toDateString(),
        ];

        $employees = Employee::whereIn('akhir_kontrak', $targetDates)->get();

        foreach ($employees as $employee) {
            $time = Carbon::now()->diffInDays($employee->akhir_kontrak);
            $subsidiaryName = optional($employee->subsidiary)->name ?? 'Tidak diketahui';
            $days = floor($time);
            $mailData = [
                'type' => 'reminder',
                'title' => 'Reminder Sisa Kontrak ' . $employee->nama,
                'body' => "Dengan Email ini kami menginformasikan bahwa karyawan dengan nama " . $employee->nama . " dari plant " . $subsidiaryName . " memiliki sisa masa kontrak " . $days . " hari lagi."
            ];
            Mail::bcc(['hrdmgr@amsgroup.co.id', 'hrd@amsgroup.co.id', 'it@amsgroup.co.id'])->queue(new MyTestMail($mailData));
            Log::info("Reminder sisa kontrak karyawan {$employee->nama} berhasil dikirim." . now());
        }

        $vehicles = Vehicle::with('subsidiary')->get();
        $now = Carbon::now();

        $defaultEmails = [
            'it@amsgroup.co.id',
            'samgowok@gmail.com',
            'hrd@eln.amsgroup.co.id',
            'hrd@amsgroup.co.id',
        ];

        foreach ($vehicles as $vehicle) {
            $reminders = [
                'STNK'     => $vehicle->stnk,
                'Pajak'    => $vehicle->pajak,
                'KIR'      => $vehicle->kir,
                'Asuransi' => $vehicle->jth_tempo,
            ];

            foreach ($reminders as $type => $date) {
                if (!$date) continue; // skip jika tanggal kosong/null
                $daysLeft = $now->diffInDays(Carbon::parse($date), false);
                if (in_array($daysLeft, [30, 15])) {
                    $mailData = [
                        'type'  => 'vehicle',
                        'title' => "Reminder Perpanjangan {$type} - {$vehicle->jenis_kendaraan}",
                        'nopol' => $vehicle->nopol,
                        'body'  => "Dengan email ini kami menginformasikan bahwa masa berlaku {$type} untuk kendaraan {$vehicle->jenis_kendaraan} dengan nopol {$vehicle->nopol} dari plant " . optional($vehicle->subsidiary)->name . " tinggal {$daysLeft} hari lagi."
                    ];

                    $to  = $defaultEmails[0];
                    $bcc = array_slice($defaultEmails, 1);

                    Mail::to($to)
                        ->bcc($bcc)
                        ->queue(new MyTestMail($mailData));

                    Log::info("Reminder {$type} untuk {$vehicle->nopol} berhasil dikirim ({$daysLeft} hari lagi) - " . now());
                }
            }
        }
    }
}
