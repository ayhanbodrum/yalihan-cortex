<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculatorFavorite extends Model
{
    use HasFactory;

    protected $table = 'calculator_favorites';

    protected $fillable = [
        'user_id',
        'calculation_type',
        'favorite_name',
        'input_data',
        'description',
    ];

    protected $casts = [
        'input_data' => 'array',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hesaplama türü adını getir
     */
    public function getCalculationTypeNameAttribute(): string
    {
        $types = [
            'price_per_sqm' => 'Metrekare Bazlı Fiyat',
            'price_per_room' => 'Oda Sayısı Bazlı Fiyat',
            'location_price' => 'Konum Bazlı Fiyat',
            'mortgage_loan' => 'Konut Kredisi',
            'vehicle_loan' => 'Taşıt Kredisi',
            'personal_loan' => 'İhtiyaç Kredisi',
            'roi_calculation' => 'ROI Hesaplama',
            'rental_yield' => 'Kira Getirisi',
            'value_appreciation' => 'Değer Artışı',
            'vat_calculation' => 'KDV Hesaplama',
            'property_tax' => 'Emlak Vergisi',
            'deed_fee' => 'Tapu Harcı',
            'sales_commission' => 'Satış Komisyonu',
            'rental_commission' => 'Kiralama Komisyonu',
            'taks_calculation' => 'TAKS Hesaplama',
            'kaks_calculation' => 'KAKS Hesaplama',
            'currency_converter' => 'Döviz Çevirici',
        ];

        return $types[$this->calculation_type] ?? $this->calculation_type;
    }

    /**
     * Formatlanmış tarih
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    /**
     * Kısa açıklama
     */
    public function getShortDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $type = $this->calculation_type_name;
        $input = $this->input_data;

        switch ($this->calculation_type) {
            case 'price_per_sqm':
                return "{$type}: {$input['metrekare']} m² × {$input['birim_fiyat']} TL";
            case 'price_per_room':
                return "{$type}: {$input['oda_sayisi']} oda × {$input['oda_basi_fiyat']} TL";
            case 'mortgage_loan':
                return "{$type}: {$input['kredi_tutari']} TL, {$input['vade']} yıl";
            case 'roi_calculation':
                return "{$type}: {$input['yatirim_tutari']} TL yatırım";
            case 'vat_calculation':
                return "{$type}: {$input['kdvsiz_fiyat']} TL, %{$input['kdv_orani']} KDV";
            default:
                return $type;
        }
    }
}
