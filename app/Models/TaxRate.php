<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $table = 'tax_rates';

    protected $fillable = [
        'tax_type',
        'tax_name',
        'rate',
        'description',
        'status',
        'effective_date',
        'expiry_date',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'status' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Aktif vergi oranları
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Geçerli tarih aralığındaki vergi oranları
     */
    public function scopeCurrent($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('effective_date')
                ->orWhere('effective_date', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>=', $today);
        });
    }

    /**
     * Vergi türüne göre filtrele
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('tax_type', $type);
    }

    /**
     * Formatlanmış oran
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 2).'%';
    }

    /**
     * Vergi türü adları
     */
    public static function getTaxTypes(): array
    {
        return [
            'vat' => 'KDV',
            'property_tax' => 'Emlak Vergisi',
            'deed_fee' => 'Tapu Harcı',
            'stamp_duty' => 'Damga Vergisi',
            'income_tax' => 'Gelir Vergisi',
            'corporate_tax' => 'Kurumlar Vergisi',
            'other' => 'Diğer',
        ];
    }

    /**
     * Vergi türü adını getir
     */
    public function getTaxTypeNameAttribute(): string
    {
        $types = self::getTaxTypes();

        return $types[$this->tax_type] ?? $this->tax_type;
    }
}
