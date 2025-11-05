<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['admin', 'superadmin', 'danisman']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'property_id' => 'nullable|integer',
            'property_type' => 'required|string|max:50',
            'transaction_type' => 'required|in:satis,kira',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:TRY,USD,EUR',
            'price_per_m2' => 'nullable|numeric|min:0',
            'il_id' => 'required|integer|exists:iller,id',
            'ilce_id' => 'nullable|integer|exists:ilceler,id',
            'mahalle_id' => 'nullable|integer|exists:mahalleler,id',
            'area_m2' => 'nullable|numeric|min:0',
            'room_count' => 'nullable|string|max:20',
            'building_age' => 'nullable|integer|min:0',
            'floor' => 'nullable|integer',
            'building_floors' => 'nullable|integer|min:0',
            'heating_type' => 'nullable|string|max:50',
            'features' => 'nullable|array',
            'market_analysis' => 'nullable|string|max:1000',
            'source' => 'required|string|max:100',
            'confidence_level' => 'required|integer|min:1|max:10',
            'is_verified' => 'boolean',
            'recorded_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'property_type.required' => 'Emlak tipi zorunludur.',
            'transaction_type.required' => 'İşlem tipi zorunludur.',
            'transaction_type.in' => 'Geçersiz işlem tipi.',
            'price.required' => 'Fiyat zorunludur.',
            'price.numeric' => 'Fiyat sayısal bir değer olmalıdır.',
            'price.min' => 'Fiyat 0 veya daha büyük olmalıdır.',
            'currency.required' => 'Para birimi zorunludur.',
            'currency.in' => 'Geçersiz para birimi.',
            'il_id.required' => 'İl seçimi zorunludur.',
            'il_id.exists' => 'Seçilen il bulunamadı.',
            'ilce_id.exists' => 'Seçilen ilçe bulunamadı.',
            'mahalle_id.exists' => 'Seçilen mahalle bulunamadı.',
            'area_m2.numeric' => 'Alan sayısal bir değer olmalıdır.',
            'area_m2.min' => 'Alan 0 veya daha büyük olmalıdır.',
            'building_age.integer' => 'Bina yaşı sayısal bir değer olmalıdır.',
            'building_age.min' => 'Bina yaşı 0 veya daha büyük olmalıdır.',
        ];
    }
}

