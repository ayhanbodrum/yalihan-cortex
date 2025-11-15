<?php

namespace App\Modules\TakimYonetimi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gorev extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gorevler';

    protected $fillable = [
        'baslik',
        'aciklama',
        'oncelik',
        'status',
        'tip',
        'bitis_tarihi',
        'tahmini_sure',
        'admin_id',
        'danisman_id',
        'musteri_id',
        'proje_id',
        'tags',
        'metadata',
    ];

    protected $casts = [
        'bitis_tarihi' => 'datetime',
        'tahmini_sure' => 'integer',
        'tags' => 'array',
        'metadata' => 'array',
    ];

    // Enum değerleri
    public static function getOncelikler(): array
    {
        return ['acil', 'yuksek', 'normal', 'dusuk'];
    }

    public static function getDurumlar(): array
    {
        return ['bekliyor', 'devam_ediyor', 'tamamlandi', 'iptal', 'beklemede'];
    }

    public static function getTipler(): array
    {
        return ['musteri_takibi', 'ilan_hazirlama', 'musteri_ziyareti', 'dokuman_hazirlama', 'diger'];
    }

    // Relationships
    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }

    public function danisman(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'danisman_id');
    }

    public function musteri(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Kisi::class, 'musteri_id');
    }

    public function proje(): BelongsTo
    {
        return $this->belongsTo(Proje::class, 'proje_id');
    }

    public function gorevTakip(): HasMany
    {
        return $this->hasMany(GorevTakip::class, 'gorev_id');
    }

    public function dosyalar(): HasMany
    {
        return $this->hasMany(GorevDosya::class, 'gorev_id');
    }

    public function statusTakip(): HasOne
    {
        return $this->hasOne(GorevTakip::class, 'gorev_id')
            ->where('status', '!=', 'tamamlandi')
            ->latest();
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['bekliyor', 'devam_ediyor']);
    }

    public function scopeOncelik($query, $oncelik)
    {
        return $query->where('oncelik', $oncelik);
    }

    public function scopeTip($query, $tip)
    {
        return $query->where('tip', $tip);
    }

    public function scopeDanisman($query, $danismanId)
    {
        return $query->where('danisman_id', $danismanId);
    }

    public function scopeDeadlineYaklasan($query, $gun = 1)
    {
        return $query->where('bitis_tarihi', '<=', now()->addDays($gun))
            ->where('status', '!=', 'tamamlandi');
    }

    public function scopeGeciken($query)
    {
        return $query->where('bitis_tarihi', '<', now())
            ->where('status', '!=', 'tamamlandi');
    }

    // Accessors
    public function getOncelikEtiketiAttribute(): string
    {
        $etiketler = [
            'acil' => '<span class="badge bg-danger">Acil</span>',
            'yuksek' => '<span class="badge bg-warning">Yüksek</span>',
            'normal' => '<span class="badge bg-info">Normal</span>',
            'dusuk' => '<span class="badge bg-secondary">Düşük</span>',
        ];

        return $etiketler[$this->oncelik] ?? $etiketler['normal'];
    }

    public function getDurumEtiketiAttribute(): string
    {
        $etiketler = [
            'bekliyor' => '<span class="badge bg-warning">Bekliyor</span>',
            'devam_ediyor' => '<span class="badge bg-primary">Devam Ediyor</span>',
            'tamamlandi' => '<span class="badge bg-success">Tamamlandı</span>',
            'iptal' => '<span class="badge bg-danger">İptal</span>',
            'beklemede' => '<span class="badge bg-secondary">Beklemede</span>',
        ];

        return $etiketler[$this->status] ?? $etiketler['bekliyor'];
    }

    public function getTipEtiketiAttribute(): string
    {
        $etiketler = [
            'musteri_takibi' => '<span class="badge bg-info">Müşteri Takibi</span>',
            'ilan_hazirlama' => '<span class="badge bg-primary">İlan Hazırlama</span>',
            'musteri_ziyareti' => '<span class="badge bg-success">Müşteri Ziyareti</span>',
            'dokuman_hazirlama' => '<span class="badge bg-warning">Doküman Hazırlama</span>',
            'diger' => '<span class="badge bg-secondary">Diğer</span>',
        ];

        return $etiketler[$this->tip] ?? $etiketler['diger'];
    }

    public function getGecikmeDurumuAttribute(): string
    {
        if ($this->status === 'tamamlandi') {
            return 'tamamlandi';
        }

        if (! $this->deadline) {
            return 'deadline_yok';
        }

        if ($this->deadline < now()) {
            return 'gecikti';
        }

        if ($this->deadline <= now()->addDay()) {
            return 'yaklasiyor';
        }

        return 'normal';
    }

    public function getGecikmeGunuAttribute(): ?int
    {
        if (! $this->deadline || $this->status === 'tamamlandi') {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    // Methods
    public function geciktiMi(): bool
    {
        return $this->deadline && $this->deadline < now() && $this->status !== 'tamamlandi';
    }

    public function deadlineYaklasiyorMu(int $gun = 1): bool
    {
        return $this->deadline &&
               $this->deadline <= now()->addDays($gun) &&
               $this->status !== 'tamamlandi';
    }

    public function tamamlanabilirMi(): bool
    {
        return in_array($this->status, ['bekliyor', 'devam_ediyor']);
    }

    public function atanabilirMi(): bool
    {
        return $this->status === 'bekliyor' && ! $this->danisman_id;
    }

    protected function deadline(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value, array $attributes) {
                return isset($attributes['bitis_tarihi']) ? \Illuminate\Support\Carbon::parse($attributes['bitis_tarihi']) : null;
            },
            set: function ($value) {
                return ['bitis_tarihi' => $value];
            }
        );
    }
}
