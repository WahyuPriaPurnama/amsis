<?php

namespace Database\Seeders;

use App\Models\Purchasing\RequestPayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RenumberRequestPaymentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Ambil seluruh transaksi, urutkan berdasarkan tanggal
            $allPayments = RequestPayment::orderBy('date', 'asc')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // 2. Kelompokkan data per (Subsidiary + Tahun)
            $grouped = $allPayments->groupBy(function ($payment) {
                $year = date('Y', strtotime($payment->date ?? $payment->created_at));
                return $payment->subsidiary_id . '_' . $year;
            });

            // 3. Loop untuk memperbarui penomoran
            foreach ($grouped as $key => $payments) {
                $sequence = 1; // Reset ke 1 setiap berganti tahun / subsidiary

                foreach ($payments as $payment) {
                    $yearMonth = date('Ym', strtotime($payment->date ?? $payment->created_at));

                    // Format: YYYYMM/0001
                    $newPaymentNumber = sprintf('%s/%04d', $yearMonth, $sequence);

                    $payment->update([
                        'payment_number' => $newPaymentNumber,
                    ]);

                    $sequence++;
                }
            }
        });

        $this->command->info('Nomor Request Payment berhasil diperbarui (Format: YYYYMM/0001)!');
    }
}
