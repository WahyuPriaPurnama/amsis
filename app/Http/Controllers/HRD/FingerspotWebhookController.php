<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\HRD\AttendanceEmployee;
use App\Models\HRD\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FingerspotWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Fingerspot Webhook Received:', $payload);

            $type    = $payload['type'] ?? null;
            $cloudId = $payload['cloud_id'] ?? null;
            $data    = $payload['data'] ?? [];

            // 1. Dapatkan subsidiary_id berdasarkan cloud_id
            $subsidiaryId = $this->resolveSubsidiaryId($cloudId);

            // -------------------------------------------------------------
            // AUTO FIX DATA LAMA: Jika subsidiary_id terdeteksi, update record
            // di database yang subsidiary_id-nya masih NULL untuk cloud_id ini
            // -------------------------------------------------------------
            if ($cloudId && $subsidiaryId) {
                AttendanceLog::where('cloud_id', $cloudId)
                    ->whereNull('subsidiary_id')
                    ->update(['subsidiary_id' => $subsidiaryId]);

                AttendanceEmployee::where('cloud_id', $cloudId)
                    ->whereNull('subsidiary_id')
                    ->update(['subsidiary_id' => $subsidiaryId]);
            }

            // -------------------------------------------------------------
            // SCENARIO A: SCAN ABSENSI REALTIME (attlog)
            // -------------------------------------------------------------
            if ($type === 'attlog' && is_array($data)) {
                $pin      = $data['pin'] ?? null;
                $scanDate = $data['scan_date'] ?? $data['scan'] ?? null;

                if ($pin && $scanDate) {
                    // Cek/Daftarkan karyawan ke tabel attendance_employees
                    $attendanceEmployee = AttendanceEmployee::firstOrCreate(
                        ['pin' => $pin],
                        [
                            'cloud_id'      => $cloudId,
                            'subsidiary_id' => $subsidiaryId,
                        ]
                    );

                    // Jika nama karyawan di mesin belum tercatat, panggil API get_userinfo secara otomatis
                    if (empty($attendanceEmployee->name_in_machine)) {
                        $this->requestUserInfoFromMachine($cloudId, $pin);
                    }

                    // Simpan Log Absensi ke tabel attendance_logs
                    AttendanceLog::updateOrCreate(
                        [
                            'pin'       => $pin,
                            'scan_date' => $scanDate,
                        ],
                        [
                            'cloud_id'      => $cloudId,
                            'subsidiary_id' => $subsidiaryId,
                            'verify'        => $data['verify'] ?? null,
                            'status_scan'   => $data['status_scan'] ?? null,
                            'photo_url'     => $data['photo_url'] ?? null,
                        ]
                    );
                }
            }

            // -------------------------------------------------------------
            // SCENARIO B: CALLBACK GET USERINFO (Respon Nama Karyawan dari Mesin)
            // -------------------------------------------------------------
            elseif ($type === 'get_userinfo' && is_array($data)) {
                $pin  = $data['pin'] ?? null;
                $name = $data['name'] ?? null;

                if ($pin && $name) {
                    // Update / Masukkan nama karyawan ke tabel attendance_employees
                    AttendanceEmployee::updateOrCreate(
                        ['pin' => $pin],
                        [
                            'name_in_machine' => $name,
                            'cloud_id'        => $cloudId,
                            'subsidiary_id'   => $subsidiaryId,
                        ]
                    );
                }
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Throwable $e) {
            Log::error('Fingerspot Webhook Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memanggil API Fingerspot get_userinfo untuk mendapatkan nama karyawan berdasarkan PIN
     */
    private function requestUserInfoFromMachine($cloudId, $pin)
    {
        try {
            Http::withHeaders([
                'Authorization' => 'Bearer K0WFK7TV51AD7934',
                'Content-Type'  => 'application/json',
            ])->post('https://developer.fingerspot.io/api/get_userinfo', [
                'trans_id' => (string) time() . '_' . $pin,
                'cloud_id' => $cloudId,
                'pin'      => (string) $pin,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed auto get_userinfo for PIN {$pin}: " . $e->getMessage());
        }
    }

    /**
     * Pemetaan Cloud ID Mesin ke ID Subsidiary
     */
    private function resolveSubsidiaryId(?string $cloudId): ?int
    {
        return match ($cloudId) {
            'S129000614' => 10, // Plant / Subsidiary ID 10
            default      => null,
        };
    }
}
