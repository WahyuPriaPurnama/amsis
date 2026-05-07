<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\HRD\Employee;

class TransferEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Pastikan user memiliki hak akses
    }

    public function rules()
    {
        return [
            'from_subsidiary_id' => [
                'required',
                'exists:subsidiaries,id',
            ],
            'to_subsidiary_id' => [
                'required',
                'exists:subsidiaries,id',
                'different:from_subsidiary_id', // Validasi agar asal & tujuan berbeda
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validasi tambahan: Cek apakah ada karyawan di plant asal
            $hasEmployees = Employee::where('subsidiary_id', $this->from_subsidiary_id)->exists();

            if (!$hasEmployees) {
                $validator->errors()->add('from_subsidiary_id', 'Plant asal tidak memiliki karyawan untuk dipindahkan.');
            }
        });
    }

    public function messages()
    {
        return [
            'from_subsidiary_id.required' => 'Plant asal wajib dipilih.',
            'to_subsidiary_id.required' => 'Plant tujuan wajib dipilih.',
            'to_subsidiary_id.different' => 'Plant tujuan tidak boleh sama dengan plant asal.',
            'to_subsidiary_id.exists' => 'Plant tujuan tidak ditemukan.',
        ];
    }
}
