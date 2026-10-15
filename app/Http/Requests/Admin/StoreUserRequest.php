<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'teknisi', 'pelapor'])],
            'unit_id' => ['nullable', 'exists:units,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }

    /**
     * Custom message validation.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar oleh pengguna lain.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran harus salah satu dari: admin, teknisi, atau pelapor.',
            'unit_id.exists' => 'Unit yang dipilih tidak ditemukan dalam sistem.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
        ];
    }
}

