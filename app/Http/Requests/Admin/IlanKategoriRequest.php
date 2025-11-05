<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IlanKategoriRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'superadmin']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('kategori') ? $this->route('kategori')->id : null;

        return [
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:ilan_kategorileri,id' . ($id ? '|not_in:' . $id : ''),
            'seviye' => 'required|integer|in:0,1,2',
            'status' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Kategori adı zorunludur.',
            'name.max' => 'Kategori adı en fazla 255 karakter olabilir.',
            'parent_id.exists' => 'Seçilen üst kategori bulunamadı.',
            'parent_id.not_in' => 'Kategori kendisinin alt kategorisi olamaz.',
            'seviye.required' => 'Seviye seçimi zorunludur.',
            'seviye.integer' => 'Seviye sayısal bir değer olmalıdır.',
            'seviye.in' => 'Geçersiz seviye değeri.',
            'order.integer' => 'Sıralama sayısal bir değer olmalıdır.',
            'order.min' => 'Sıralama 0 veya daha büyük olmalıdır.',
        ];
    }
}

