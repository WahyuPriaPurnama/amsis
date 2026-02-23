<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TrackingsController extends Controller
{
    private $apiKey = '9ade23c8f3089874a7fe41914b6a64198310804f5e37696cec028a8552dd1b0c';

    public function index()
    {
        // Menampilkan halaman form tracking awal
        return view('purchasing.trackings');
    }

    public function track(Request $request)
    {
        $request->validate([
            'no_resi' => 'required|string|min:5',
            'courier' => 'required'
        ]);

        $resi = $request->no_resi;
        $courierCode = $request->courier;

        try {
            // Jika pilih otomatis, deteksi dulu
            if ($courierCode == 'auto') {
                $detectResponse = Http::get("https://api.binderbyte.com/v1/list_courier", [
                    'api_key' => $this->apiKey,
                    'content' => $resi
                ]);

                $courierData = $detectResponse->json();

                if ($detectResponse->successful() && !empty($courierData['data'])) {
                    // Ambil kode pertama yang disarankan
                    $courierCode = $courierData['data'][0]['code'];
                    $courierName = $courierData['data'][0]['description'];
                } else {
                    return back()->with('error', 'Kurir tidak terdeteksi otomatis. Silakan pilih kurir secara manual.');
                }
            } else {
                // Mapping nama kurir untuk tampilan (Opsional: bisa ambil dari array manual)
                $courierName = str_replace('_', ' ', strtoupper($courierCode));
            }

            // Eksekusi Tracking
            $trackingResponse = Http::get("https://api.binderbyte.com/v1/track", [
                'api_key' => $this->apiKey,
                'courier' => $courierCode,
                'awb'     => $resi
            ]);

            $result = $trackingResponse->json();

            if ($trackingResponse->successful() && isset($result['status']) && $result['status'] == 200) {
                return view('purchasing.trackings', [
                    'data'         => $result['data']['summary'],
                    'history'      => $result['data']['history'],
                    'resi'         => $resi,
                    'courier_name' => $courierName
                ]);
            }

            return back()->with('error', $result['message'] ?? 'Nomor resi tidak ditemukan untuk kurir yang dipilih.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
