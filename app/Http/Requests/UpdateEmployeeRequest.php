<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employee = $this->route('employee');
        return [
            // Gunakan 'sometimes' untuk field yang bisa di-disabled di view
            'nip' => 'required|unique:employees,nip,' . $employee->id,
            'nama' => 'required|string|max:255',
            'subsidiary_id' => 'sometimes|required|integer|exists:subsidiaries,id',
            'status_peg' => 'sometimes|required|string',

            // Field organisasi lainnya
            'divisi' => 'required|string|max:100',
            'departemen' => 'required|string|max:100',
            'seksi' => 'required|string|max:100',
            'posisi' => 'required|string|max:100',
            'tgl_masuk' => 'required|date',
            'awal_kontrak' => 'nullable|required_if:status_peg,PKWT|date',
            'akhir_kontrak' => 'nullable|required_if:status_peg,PKWT|date',

            // Biodata & Kontak
            'nik' => 'required|string|unique:employees,nik,' . $employee->id,
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'jenis_kelamin' => 'required|in:L,P',
            'status' => 'required|string',
            'jml_ank' => 'nullable|integer|min:0',

            // Validasi File (digabungkan agar rapi)
            'pp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ktp' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'kk' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'npwp2' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'bpjs_kes' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'bpjs_ket' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'ttd' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
