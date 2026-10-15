<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'nama')->ignore($this->route('unit')),
            ],
        ];
    }

    /**
     * Custom message validation.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama unit wajib diisi.',
            'nama.unique' => 'Nama unit sudah terdaftar di sistem.',
            'nama.max' => 'Nama unit tidak boleh melebihi 255 karakter.',
        ];
    }
}

