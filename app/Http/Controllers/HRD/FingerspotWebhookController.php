<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\HRD\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FingerspotWebhookController extends Controller
{
    
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();
            Log::info('Fingerspot Webhook Received:', $payload);

            $type = $payload['type'] ?? null;
            $cloudId = $payload['cloud_id'] ?? null;
            $data = $payload['data'] ?? [];

            if ($type === 'attlog' && is_array($data)) {
                AttendanceLog::updateOrCreate(
                    [
                        'pin'       => $data['pin'] ?? null,
                        'scan_date' => $data['scan_date'] ?? $data['scan'] ?? null,
                    ],
                    [
                        'cloud_id'    => $cloudId,
                        'verify'      => $data['verify'] ?? null,
                        'status_scan' => $data['status_scan'] ?? null,
                        'photo_url'   => $data['photo_url'] ?? null,
                    ]
                );
            } elseif (is_array($data) && array_is_list($data)) {
                foreach ($data as $log) {
                    if (isset($log['pin']) && isset($log['scan_date'])) {
                        AttendanceLog::updateOrCreate(
                            [
                                'pin'       => $log['pin'],
                                'scan_date' => $log['scan_date'],
                            ],
                            [
                                'cloud_id'    => $cloudId,
                                'verify'      => $log['verify'] ?? null,
                                'status_scan' => $log['status_scan'] ?? null,
                                'photo_url'   => $log['photo_url'] ?? null,
                            ]
                        );
                    }
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
}
