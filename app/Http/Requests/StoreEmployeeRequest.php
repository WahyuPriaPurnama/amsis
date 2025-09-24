<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        return [
            //organisasi
            'nip' => 'required|string|unique:employees,nip',
            'nama' => 'required|string',
            'subsidiary_id' => 'required|integer|exists:subsidiaries,id',
            'divisi' => 'required|string|max:100',
            'departemen' => 'required|string|max:100',
            'seksi' => 'required|string|max:100',
            'posisi' => 'required|string|max:100',
            'status_peg' => 'required|string',
            'tgl_masuk' => 'required|date',
            'awal_kontrak' => 'nullable|date',
            'akhir_kontrak' => 'nullable|date',
            
            //biodata
            'nik' => 'nullable|string|unique:employees,nik',
            'tmpt_lahir' => 'nullable|string',
            'tgl_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string',
            'email' => 'nullable|string|unique:employees,email',
            'pend_trkhr' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'thn_lulus' => 'nullable|string',
            'nama_ibu' => 'nullable|string',
            'npwp' => 'nullable|string',
            'status' => 'nullable|string',
            'jml_ank' => 'nullable|string',
            'nama_kd' => 'nullable|string',
            'no_kd' => 'nullable|string',
            'hubungan' => 'nullable|string',
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
            'integer' => 'hanya boleh berisi angka',
            'alpha_num' => 'hanya boleh berisi huruf dan angka',
            'email' => 'format email tidak valid',
            'date' => 'harus berisi tanggal yang valid',
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
