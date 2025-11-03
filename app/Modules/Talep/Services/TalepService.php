<?php

namespace App\Modules\Talep\Services;

use App\Models\Ilan;
use App\Models\IlanTalepEslesme;
use App\Models\Talep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TalepService
{
    /**
     * Yeni talep oluştur
     *
     * @return Talep
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $talep = new Talep;
            $talep->fill($data);
            $talep->olusturan_id = Auth::id();
            $talep->save();

            // Otomatik eşleştirme başlat
            $this->otomatikEslestir($talep);

            DB::commit();

            Log::info('Yeni talep oluşturuldu', [
                'talep_id' => $talep->id,
                'kisi_id' => $talep->kisi_id,
                'kategori' => $talep->kategori,
            ]);

            return $talep;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Talep oluşturma hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Talep güncelle
     *
     * @return Talep
     */
    public function update(Talep $talep, array $data)
    {
        DB::beginTransaction();

        try {
            $eskiData = $talep->toArray();
            $talep->fill($data);
            $talep->guncelleyen_id = Auth::id();
            $talep->save();

            // Önemli alanlar değiştiyse yeniden eşleştir
            if ($this->onemliAlanDegisti($eskiData, $data)) {
                $this->yenidenEslestir($talep);
            }

            DB::commit();

            Log::info('Talep güncellendi', [
                'talep_id' => $talep->id,
                'degisiklikler' => array_diff_assoc($data, $eskiData),
            ]);

            return $talep;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Talep güncelleme hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Talep sil
     *
     * @return bool
     */
    public function delete(Talep $talep)
    {
        DB::beginTransaction();

        try {
            // İlişkili eşleştirmeleri sil
            $talep->eslesmeler()->delete();

            // Talebi soft delete
            $talep->delete();

            DB::commit();

            Log::info('Talep silindi', ['talep_id' => $talep->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Talep silme hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Talep statusu güncelle
     *
     * @return Talep
     */
    public function updateDurum(Talep $talep, string $status, ?string $not = null)
    {
        $eskiDurum = $talep->status;

        $talep->status = $status;
        $talep->status_notu = $not;
        $talep->status_degisim_tarihi = now();
        $talep->guncelleyen_id = Auth::id();

        if ($status === 'tamamlandi' && ! $talep->tamamlanma_tarihi) {
            $talep->tamamlanma_tarihi = now();
        }

        $talep->save();

        Log::info('Talep statusu güncellendi', [
            'talep_id' => $talep->id,
            'eski_status' => $eskiDurum,
            'yeni_status' => $status,
            'not' => $not,
        ]);

        return $talep;
    }

    /**
     * Talep-ilan eşleştirme
     *
     * @return IlanTalepEslesme
     */
    public function eslestir(Talep $talep, Ilan $ilan, ?float $skor = null, ?string $not = null)
    {
        // Mevcut eşleştirme var mı kontrol et
        $mevcutEslesme = IlanTalepEslesme::where('talep_id', $talep->id)
            ->where('ilan_id', $ilan->id)
            ->first();

        if ($mevcutEslesme) {
            // Mevcut eşleştirmeyi güncelle
            $mevcutEslesme->skor = $skor ?? $mevcutEslesme->skor;
            $mevcutEslesme->notlar = $not ?? $mevcutEslesme->notlar;
            $mevcutEslesme->guncelleyen_id = Auth::id();
            $mevcutEslesme->save();

            return $mevcutEslesme;
        }

        // Yeni eşleştirme oluştur
        $eslesme = new IlanTalepEslesme;
        $eslesme->talep_id = $talep->id;
        $eslesme->ilan_id = $ilan->id;
        $eslesme->skor = $skor ?? $this->hesaplaSkor($talep, $ilan);
        $eslesme->notlar = $not;
        $eslesme->olusturan_id = Auth::id();
        $eslesme->save();

        Log::info('Yeni eşleştirme oluşturuldu', [
            'talep_id' => $talep->id,
            'ilan_id' => $ilan->id,
            'skor' => $eslesme->skor,
        ]);

        return $eslesme;
    }

    /**
     * Otomatik eşleştirme
     *
     * @return array
     */
    public function otomatikEslestir(Talep $talep, int $limit = 10)
    {
        $uygunIlanlar = $this->getBenzerIlanlar($talep, $limit * 2);
        $eslesmeler = [];

        foreach ($uygunIlanlar as $ilan) {
            $skor = $this->hesaplaSkor($talep, $ilan);

            // Minimum skor kontrolü
            if ($skor >= 60) {
                $eslesme = $this->eslestir($talep, $ilan, $skor, 'Otomatik eşleştirme');
                $eslesmeler[] = $eslesme;

                if (count($eslesmeler) >= $limit) {
                    break;
                }
            }
        }

        Log::info('Otomatik eşleştirme tamamlandı', [
            'talep_id' => $talep->id,
            'eslesme_sayisi' => count($eslesmeler),
        ]);

        return $eslesmeler;
    }

    /**
     * Benzer ilanları getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBenzerIlanlar(Talep $talep, int $limit = 20)
    {
        $query = Ilan::where('status', 'active')
            ->where('kategori', $talep->kategori);

        // Fiyat aralığı
        if ($talep->min_fiyat) {
            $query->where('fiyat', '>=', $talep->min_fiyat);
        }
        if ($talep->max_fiyat) {
            $query->where('fiyat', '<=', $talep->max_fiyat);
        }

        // Metrekare aralığı
        if ($talep->min_metrekare) {
            $query->where('metrekare', '>=', $talep->min_metrekare);
        }
        if ($talep->max_metrekare) {
            $query->where('metrekare', '<=', $talep->max_metrekare);
        }

        // Lokasyon filtreleri
        if ($talep->il_id) {
            $query->where('il_id', $talep->il_id);
        }
        if ($talep->ilce_id) {
            $query->where('ilce_id', $talep->ilce_id);
        }
        if ($talep->mahalle_id) {
            $query->where('mahalle_id', $talep->mahalle_id);
        }

        return $query->with(['fotograflar', 'il', 'ilce', 'mahalle'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Eşleştirme skoru hesapla
     *
     * @return float
     */
    public function hesaplaSkor(Talep $talep, Ilan $ilan)
    {
        $skor = 0;
        $toplamKriter = 0;

        // Kategori eşleşmesi (zorunlu)
        if ($talep->kategori === $ilan->kategori) {
            $skor += 30;
        } else {
            return 0; // Kategori eşleşmiyorsa skor 0
        }
        $toplamKriter += 30;

        // Fiyat uyumluluğu
        if ($talep->min_fiyat && $talep->max_fiyat) {
            if ($ilan->fiyat >= $talep->min_fiyat && $ilan->fiyat <= $talep->max_fiyat) {
                $skor += 25;
            } elseif ($ilan->fiyat < $talep->min_fiyat) {
                $fark = ($talep->min_fiyat - $ilan->fiyat) / $talep->min_fiyat;
                $skor += max(0, 25 - ($fark * 25));
            } else {
                $fark = ($ilan->fiyat - $talep->max_fiyat) / $talep->max_fiyat;
                $skor += max(0, 25 - ($fark * 25));
            }
        } elseif ($talep->min_fiyat && $ilan->fiyat >= $talep->min_fiyat) {
            $skor += 25;
        } elseif ($talep->max_fiyat && $ilan->fiyat <= $talep->max_fiyat) {
            $skor += 25;
        }
        $toplamKriter += 25;

        // Metrekare uyumluluğu
        if ($talep->min_metrekare && $talep->max_metrekare) {
            if ($ilan->metrekare >= $talep->min_metrekare && $ilan->metrekare <= $talep->max_metrekare) {
                $skor += 20;
            }
        } elseif ($talep->min_metrekare && $ilan->metrekare >= $talep->min_metrekare) {
            $skor += 20;
        } elseif ($talep->max_metrekare && $ilan->metrekare <= $talep->max_metrekare) {
            $skor += 20;
        }
        $toplamKriter += 20;

        // Lokasyon uyumluluğu
        $lokasyonSkor = 0;
        if ($talep->mahalle_id && $talep->mahalle_id === $ilan->mahalle_id) {
            $lokasyonSkor = 25; // Tam mahalle eşleşmesi
        } elseif ($talep->ilce_id && $talep->ilce_id === $ilan->ilce_id) {
            $lokasyonSkor = 20; // İlçe eşleşmesi
        } elseif ($talep->il_id && $talep->il_id === $ilan->il_id) {
            $lokasyonSkor = 15; // İl eşleşmesi
        }
        $skor += $lokasyonSkor;
        $toplamKriter += 25;

        // Skoru yüzdelik olarak hesapla
        return ($skor / $toplamKriter) * 100;
    }

    /**
     * Talep aktivitelerini getir
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAktiviteler(Talep $talep)
    {
        // Bu method CRM modülü ile entegre edilecek
        return collect([]);
    }

    /**
     * Kategorileri getir
     *
     * @return array
     */
    public function getKategoriler()
    {
        return [
            'satilik' => 'Satılık',
            'kiralik' => 'Kiralık',
            'devren_satilik' => 'Devren Satılık',
            'devren_kiralik' => 'Devren Kiralık',
        ];
    }

    /**
     * Durumları getir
     *
     * @return array
     */
    public function getDurumlar()
    {
        return [
            'active' => 'Aktif',
            'beklemede' => 'Beklemede',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal',
        ];
    }

    /**
     * Toplu işlem
     *
     * @return array
     */
    public function topluIslem(array $talepIds, string $islem, string $deger)
    {
        $basarili = 0;
        $hatali = 0;
        $hatalar = [];

        DB::beginTransaction();

        try {
            foreach ($talepIds as $talepId) {
                try {
                    $talep = Talep::findOrFail($talepId);

                    switch ($islem) {
                        case 'status_guncelle':
                            $this->updateDurum($talep, $deger);
                            break;
                        case 'sil':
                            $this->delete($talep);
                            break;
                        default:
                            throw new \Exception('Geçersiz işlem: '.$islem);
                    }

                    $basarili++;
                } catch (\Exception $e) {
                    $hatali++;
                    $hatalar[] = "Talep {$talepId}: ".$e->getMessage();
                }
            }

            DB::commit();

            Log::info('Toplu işlem tamamlandı', [
                'islem' => $islem,
                'basarili' => $basarili,
                'hatali' => $hatali,
            ]);

            return [
                'basarili' => $basarili,
                'hatali' => $hatali,
                'hatalar' => $hatalar,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Toplu işlem hatası: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Önemli alan değişti mi kontrol et
     *
     * @return bool
     */
    private function onemliAlanDegisti(array $eskiData, array $yeniData)
    {
        $onemliAlanlar = [
            'kategori', 'min_fiyat', 'max_fiyat',
            'min_metrekare', 'max_metrekare',
            'il_id', 'ilce_id', 'mahalle_id',
        ];

        foreach ($onemliAlanlar as $alan) {
            if (isset($yeniData[$alan]) &&
                isset($eskiData[$alan]) &&
                $yeniData[$alan] != $eskiData[$alan]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Yeniden eşleştir
     *
     * @return void
     */
    private function yenidenEslestir(Talep $talep)
    {
        // Mevcut otomatik eşleştirmeleri sil
        $talep->eslesmeler()
            ->where('notlar', 'Otomatik eşleştirme')
            ->delete();

        // Yeniden otomatik eşleştir
        $this->otomatikEslestir($talep);
    }
}
