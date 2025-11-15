<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\KisiTipi;

/**
 * Context7: Kişi validation with Enum support
 */
class KisiRequest extends FormRequest
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
        $kisiId = $this->route('kisi') ? $this->route('kisi')->id : null;

        return [
            // ✅ REQUIRED FIELDS
            'ad' => ['required', 'string', 'max:255'],
            'soyad' => ['required', 'string', 'max:255'],
            
            // ✅ OPTIONAL FIELDS with validation
            'telefon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('kisiler')->ignore($kisiId)],
            
            // ✅ ENUM VALIDATION (Context7)
            'kisi_tipi' => ['nullable', Rule::enum(KisiTipi::class)],
            
            // ✅ LEGACY FIELD (backward compatibility)
            'musteri_tipi' => ['nullable', 'string', 'max:50'],
            
            // Status
            'status' => ['required', 'boolean'],
            
            // Adres (Global Location System)
            'il_id' => ['nullable', 'integer', 'exists:iller,id'],
            'ilce_id' => ['nullable', 'integer', 'exists:ilceler,id'],
            'mahalle_id' => ['nullable', 'integer', 'exists:mahalleler,id'],
            
            // Danışman
            'danisman_id' => ['nullable', 'integer', 'exists:users,id'],
            
            // CRM Fields
            'tc_kimlik' => ['nullable', 'string', 'size:11'],
            'dogum_tarihi' => ['nullable', 'date'],
            'meslek' => ['nullable', 'string', 'max:100'],
            'gelir_duzeyi' => ['nullable', 'string', 'max:50'],
            'vergi_no' => ['nullable', 'string', 'max:20'],
            'vergi_dairesi' => ['nullable', 'string', 'max:100'],
            
            // Notlar
            'notlar' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'ad' => 'Ad',
            'soyad' => 'Soyad',
            'telefon' => 'Telefon',
            'email' => 'E-posta',
            'kisi_tipi' => 'Kişi Tipi',
            'musteri_tipi' => 'Müşteri Tipi',
            'status' => 'Durum',
            'il_id' => 'İl',
            'ilce_id' => 'İlçe',
            'mahalle_id' => 'Mahalle',
            'danisman_id' => 'Danışman',
            'tc_kimlik' => 'TC Kimlik No',
            'dogum_tarihi' => 'Doğum Tarihi',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'ad.required' => 'Ad alanı zorunludur.',
            'soyad.required' => 'Soyad alanı zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi zaten kullanılıyor.',
            'tc_kimlik.size' => 'TC Kimlik No 11 haneli olmalıdır.',
            'kisi_tipi.enum' => 'Geçersiz kişi tipi seçildi.',
        ];
    }
}


















