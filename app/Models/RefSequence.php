<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ref Sequence Model
 *
 * Context7 Standardı: C7-REF-SEQUENCE-MODEL-2025-11-05
 *
 * Benzersiz referans numarası üretimi için sequence yönetimi
 */
class RefSequence extends Model
{
    protected $table = 'ref_sequences';

    protected $fillable = [
        'sequence_key',
        'last_sequence',
        'year',
        'yayin_tipi',
        'lokasyon_kodu',
        'kategori_kodu',
        'last_used_at',
    ];

    protected $casts = [
        'last_sequence' => 'integer',
        'year' => 'integer',
        'last_used_at' => 'datetime',
    ];

    /**
     * Sequence key oluştur
     *
     * @param string $yayinTipi
     * @param string $lokasyonKodu
     * @param string $kategoriKodu
     * @param int|null $year
     * @return string
     */
    public static function generateSequenceKey(
        string $yayinTipi,
        string $lokasyonKodu,
        string $kategoriKodu,
        ?int $year = null
    ): string {
        $year = $year ?? date('Y');

        return sprintf(
            '%s-%s-%s-%d',
            $yayinTipi,
            $lokasyonKodu,
            $kategoriKodu,
            $year
        );
    }

    /**
     * Sonraki sequence numarasını al (thread-safe)
     *
     * @param string $sequenceKey
     * @return int
     */
    public static function getNextSequence(string $sequenceKey): int
    {
        return \DB::transaction(function () use ($sequenceKey) {
            $sequence = self::lockForUpdate()
                ->where('sequence_key', $sequenceKey)
                ->first();

            if (!$sequence) {
                // Yeni sequence oluştur
                $parts = explode('-', $sequenceKey);
                $sequence = self::create([
                    'sequence_key' => $sequenceKey,
                    'last_sequence' => 0,
                    'year' => end($parts) ?? date('Y'),
                    'yayin_tipi' => $parts[0] ?? null,
                    'lokasyon_kodu' => $parts[1] ?? null,
                    'kategori_kodu' => $parts[2] ?? null,
                    'last_used_at' => now(),
                ]);
            }

            // Sequence'ı artır ve kaydet
            $sequence->last_sequence++;
            $sequence->last_used_at = now();
            $sequence->save();

            return $sequence->last_sequence;
        });
    }

    /**
     * Sequence'ı sıfırla (yıl bazlı)
     *
     * @param int $year
     * @return int
     */
    public static function resetYearlySequences(int $year): int
    {
        return self::where('year', $year - 1)
            ->update(['last_sequence' => 0]);
    }
}
