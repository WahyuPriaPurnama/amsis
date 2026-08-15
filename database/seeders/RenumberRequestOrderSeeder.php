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

            // 2. Kelompokkan data per (Subsidiary + Tahun)
            $grouped = $allOrders->groupBy(function ($order) {
                $year = date('Y', strtotime($order->request_date ?? $order->created_at));
                return $order->subsidiary_id . '_' . $year;
            });

            // 3. Loop per pembaruan nomor
            foreach ($grouped as $key => $orders) {
                $sequence = 1; // Reset ke 1 tiap ganti subsidiary/tahun

                foreach ($orders as $order) {
                    $yearMonth = date('Ym', strtotime($order->request_date ?? $order->created_at));

                    // Format: YYYYMM/0001
                    $newRequestNumber = sprintf('%s/%04d', $yearMonth, $sequence);

                    $order->update([
                        'request_number' => $newRequestNumber,
                    ]);

                    $sequence++;
                }
            }
        });

        $this->command->info('Semua nomor Request Order berhasil diperbarui (Format: YYYYMM/0001)!');
    }
}
