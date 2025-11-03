<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionRate extends Model
{
    use HasFactory;

    protected $table = 'commission_rates';

    protected $fillable = [
        'commission_type',
        'commission_name',
        'rate',
        'min_amount',
        'max_amount',
        'description',
        'status',
        'effective_date',
        'expiry_date',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'status' => 'boolean',
        'effective_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Aktif komisyon oranları
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Geçerli tarih aralığındaki komisyon oranları
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
     * Komisyon türüne göre filtrele
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('commission_type', $type);
    }

    /**
     * Tutar aralığına göre filtrele
     */
    public function scopeByAmount($query, float $amount)
    {
        return $query->where(function ($q) use ($amount) {
            $q->whereNull('min_amount')
                ->orWhere('min_amount', '<=', $amount);
        })->where(function ($q) use ($amount) {
            $q->whereNull('max_amount')
                ->orWhere('max_amount', '>=', $amount);
        });
    }

    /**
     * Formatlanmış oran
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 2).'%';
    }

    /**
     * Formatlanmış tutar aralığı
     */
    public function getFormattedAmountRangeAttribute(): string
    {
        if ($this->min_amount && $this->max_amount) {
            return number_format($this->min_amount, 0, ',', '.').' - '.
                   number_format($this->max_amount, 0, ',', '.').' TL';
        } elseif ($this->min_amount) {
            return number_format($this->min_amount, 0, ',', '.').' TL ve üzeri';
        } elseif ($this->max_amount) {
            return number_format($this->max_amount, 0, ',', '.').' TL ve altı';
        }

        return 'Tüm tutarlar';
    }

    /**
     * Komisyon türü adları
     */
    public static function getCommissionTypes(): array
    {
        return [
            'sales' => 'Satış Komisyonu',
            'rental' => 'Kiralama Komisyonu',
            'consultation' => 'Danışmanlık Komisyonu',
            'management' => 'Yönetim Komisyonu',
            'brokerage' => 'Acentelik Komisyonu',
            'other' => 'Diğer',
        ];
    }

    /**
     * Komisyon türü adını getir
     */
    public function getCommissionTypeNameAttribute(): string
    {
        $types = self::getCommissionTypes();

        return $types[$this->commission_type] ?? $this->commission_type;
    }
}
