<?php

namespace Database\Seeders;

use App\Models\Purchasing\RequestOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RenumberRequestOrderSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Ambil seluruh data RequestOrder, urutkan berdasarkan request_date
            $allOrders = RequestOrder::orderBy('request_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // 2. Kelompokkan data per (Subsidiary + Tahun + Bulan)
            $grouped = $allOrders->groupBy(function ($order) {
                $yearMonth = date('Ym', strtotime($order->request_date ?? $order->created_at));
                return $order->subsidiary_id . '_' . $yearMonth;
            });

            // 3. Loop untuk pembaruan nomor
            foreach ($grouped as $key => $orders) {
                $sequence = 1; // Reset ke 1 tiap berganti subsidiary atau bulan

                foreach ($orders as $order) {
                    $yearMonth = date('Ym', strtotime($order->request_date ?? $order->created_at));

                    // Format: YYYYMM/001 (3 Digit)
                    $newRequestNumber = sprintf('%s/%03d', $yearMonth, $sequence);

                    $order->update([
                        'request_number' => $newRequestNumber,
                    ]);

                    $sequence++;
                }
            }
        });

        $this->command->info('Semua nomor Request Order berhasil diperbarui (Reset per Bulan/Subsidiary, Format: YYYYMM/001)!');
    }
}