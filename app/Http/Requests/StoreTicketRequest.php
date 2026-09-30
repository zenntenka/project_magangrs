<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'unit_id' => ['required', 'exists:units,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'prioritas' => ['required', 'in:rendah,sedang,tinggi'],
            'lampiran' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'unit_id.required' => 'Unit rumah sakit wajib dipilih.',
            'unit_id.exists' => 'Unit yang dipilih tidak valid.',
            'category_id.required' => 'Kategori kendala wajib dipilih.',
            'category_id.exists' => 'Kategori kendala tidak valid.',
            'judul.required' => 'Judul kendala wajib diisi.',
            'judul.max' => 'Judul kendala maksimal 255 karakter.',
            'deskripsi.required' => 'Deskripsi kendala wajib diisi.',
            'prioritas.required' => 'Tingkat prioritas wajib dipilih.',
            'prioritas.in' => 'Pilihan prioritas tidak valid.',
            'lampiran.image' => 'Lampiran harus berupa file gambar.',
            'lampiran.mimes' => 'Format gambar yang diperbolehkan hanya JPG atau PNG.',
            'lampiran.max' => 'Ukuran gambar lampiran maksimal 2MB.',
        ];
    }
}
