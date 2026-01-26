<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'condition'          => 'required|in:Baik,Rusak,Lainnya',
            'owner'              => 'required|in:Umum,Engineering,QC & Lab',
            'location'           => 'required|string|max:255',
            'category'           => 'required|in:Tanah & Bangunan,Mesin,Furniture & Fixture,Kendaraan,Alat Kerja,Fasilitas',
            'accounting_code'    => 'nullable|string|max:255',
            'quantity'           => 'required|integer|min:1',
            'unit'               => 'required|string|max:50',
            'usage_date'         => 'nullable|date',
            'purchase_date'      => 'nullable|date',
            'purchase_value'     => 'nullable|numeric|min:0',
            'depreciation_value' => 'nullable|numeric|min:0',
            'total_value'        => 'nullable|numeric|min:0',
            'useful_life'        => 'nullable|integer|min:0',
            'subsidiary_id'      => 'required|exists:subsidiaries,id',
            // file upload
            'delivery_receipt'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'manual_book'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'attachment'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        if ($this->isMethod('post')) {
            // Store: code harus unik per subsidiary
            $rules['code'] = 'required|string|max:255|unique:assets,code,NULL,id,subsidiary_id,' . $this->input('subsidiary_id');
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Update: abaikan id asset saat validasi unique
            $asset = $this->route('asset'); // bisa model atau ID tergantung route
            $assetId = $asset instanceof \App\Models\HRD\Asset ? $asset->id : $asset;

            $rules['code'] = 'required|string|max:255|unique:assets,code,'
                . $assetId . ',id,subsidiary_id,' . $this->input('subsidiary_id');
        }

        return $rules;
    }
}
