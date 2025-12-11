<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubsidiaryRequest extends FormRequest
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
    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'kop_header' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'kop_footer' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ];

        // Jika update, tambahkan pengecualian unik berdasarkan ID
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $id = $this->route('subsidiary'); // pastikan nama route param sesuai
            $rules['email'] .= ",email,{$id}";
            $rules['npwp'] .= ",npwp,{$id}";
        }

        return $rules;
    }
    public function messages()
    {
        return [
            'required' => 'wajib diisi',
            'unique' => 'tidak boleh sama',
            'name.max' => 'nama maksimal 50 karakter',
            'name.min' => 'nama minimal 3 karakter',
            'logo.mimes' => 'format yang diizinkan png, jpeg atau jpg',
            'logo.max' => 'maksimal ukuran foto 2 MB',
            'email.email' => 'format email tidak valid',
            'kop_header.mimes' => 'format yang diizinkan png, jpeg atau jpg',
            'kop_header.max' => 'maksimal ukuran foto 2 MB',
            'kop_footer.mimes' => 'format yang diizinkan png, jpeg atau jpg',
            'kop_footer.max' => 'maksimal ukuran foto 2 MB',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strtoupper($this->name)
        ]);
    }
}
