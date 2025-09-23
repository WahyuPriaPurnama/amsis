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
            'nip' => 'required|unique:employees,nip,' . $employee->id,
            'nama' => 'required',
            'nik' => 'required|string|unique:employees,nik,' . $employee->id,
            'subsidiary_id' => 'required|integer|exists:subsidiaries,id',
            'divisi' => 'required|string|max:100',
            'departemen' => 'required|string|max:100',
            'seksi' => 'required|string|max:100',
            'posisi' => 'required|string|max:100',
            'status_peg' => 'required|string',
            'tgl_masuk' => 'required|date',
            'awal_kontrak' => 'nullable|date',
            'akhir_kontrak' => 'nullable|date',
            'tmpt_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telp' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'pend_trkhr' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'thn_lulus' => 'nullable|string',
            'nama_ibu' => 'required|string',
            'npwp' => 'nullable|string',
            'status' => 'required|string',
            'jml_ank' => 'nullable|string',
            'nama_kd' => 'required|string',
            'no_kd' => 'required|string',
            'hubungan' => 'required|string',
            'pp' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'ktp' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'kk' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'npwp2' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'bpjs_kes' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
            'bpjs_ket' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'wajib diisi',
            'unique' => 'tidak boleh sama',
            'nip.unique' => 'NIP sudah terdaftar',
            'nik.unique' => 'NIK sudah terdaftar',
            'email.unique' => 'Email sudah terdaftar',
            'pp.mimes' => 'format yang diizinkan png, jpg dan jpeg',
            'mimes' => 'format yang diizinkan png, jpg, jpeg dan pdf',
            'max' => 'ukuran file maksimum 2 MB',
            'email' => 'format email tidak valid',
            'string' => 'harus berupa teks'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nama' => ucwords(strtolower($this->nama)),
            'tmpt_lahir' => ucwords(strtolower($this->tmpt_lahir)),
            'jurusan' => ucwords(strtolower($this->jurusan)),
            'nama_ibu' => ucwords(strtolower($this->nama_ibu)),
            'nama_kd' => ucwords(strtolower($this->nama_kd)),
            'hubungan' => ucwords(strtolower($this->hubungan)),
            'email' => strtolower($this->email),
        ]);
    }
}
