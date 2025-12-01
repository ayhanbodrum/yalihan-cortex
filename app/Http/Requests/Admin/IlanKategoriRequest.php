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
        $routeParam = $this->route('kategori');
        $id = is_object($routeParam) ? ($routeParam->id ?? null) : (is_numeric($routeParam) ? (int) $routeParam : null);
        $seviye = $this->input('seviye');

        return [
            'name' => 'required|string|max:255',
            'parent_id' => [
                'nullable',
                'exists:ilan_kategorileri,id'.($id ? '|not_in:'.$id : ''),
                // Context7: Alt kategori (seviye=1) için parent_id zorunlu
                function ($attribute, $value, $fail) use ($seviye) {
                    if (($seviye == 1 || $seviye == 2) && ! $value) {
                        $fail('Alt kategori veya Yayın Tipi için Üst Kategori seçmelisiniz.');
                    }
                    if ($seviye == 0 && $value) {
                        $fail('Ana kategorinin üst kategorisi olamaz.');
                    }
                },
            ],
            'seviye' => 'required|integer|in:0,1,2',
            'status' => 'nullable|boolean', // Context7: status kullanımı
            'display_order' => 'nullable|integer|min:0', // ✅ Context7: order → display_order
            'slug' => 'nullable|string|max:255|unique:ilan_kategorileri,slug'.($id ? ','.$id : ''),
            'icon' => 'nullable|string|max:100',
            'aciklama' => 'nullable|string|max:500',
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
            'display_order.integer' => 'Sıralama sayısal bir değer olmalıdır.', // ✅ Context7: order → display_order
            'display_order.min' => 'Sıralama 0 veya daha büyük olmalıdır.', // ✅ Context7: order → display_order
        ];
    }
}
