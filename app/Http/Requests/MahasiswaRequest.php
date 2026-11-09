<?php

namespace App\Http\Requests;

use App\Models\Mahasiswa;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MahasiswaRequest extends FormRequest
{
    /**
     * Permission is enforced by the controller's middleware before this request runs.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nrp' => ['required', 'digits:10', Rule::unique('mahasiswas')->ignore($this->route('mahasiswa'))],
            'nama' => ['required', 'string', 'max:255'],
            'departemen' => ['required', Rule::in(Mahasiswa::DEPARTEMEN)],
            'dosen_id' => ['required', 'exists:dosens,id'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
