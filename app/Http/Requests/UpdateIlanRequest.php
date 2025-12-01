<?php

namespace App\Http\Requests;

use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Services\CategoryFieldValidator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $kategoriSlug = null;
        $yayinTipiSlug = null;
        $anaId = $this->input('ana_kategori_id');
        $yayinId = $this->input('yayin_tipi_id');
        if ($anaId) {
            $ana = IlanKategori::find($anaId);
            $kategoriSlug = $ana ? strtolower($ana->slug ?? '') : null;
        }
        if ($yayinId) {
            $yayin = IlanKategoriYayinTipi::find($yayinId);
            $yayinTipiSlug = $yayin ? strtolower($yayin->slug ?? '') : null;
        }

        $base = [
            'baslik' => 'required|string|max:255',
            'aciklama' => 'nullable|string',
            'fiyat' => 'required|numeric|min:0',
            'para_birimi' => 'required|string|in:TRY,USD,EUR,GBP',
            'ana_kategori_id' => 'required|exists:ilan_kategorileri,id',
            'alt_kategori_id' => 'required|exists:ilan_kategorileri,id',
            'yayin_tipi_id' => 'required|integer|exists:ilan_kategori_yayin_tipleri,id',
            'ilan_sahibi_id' => 'required|exists:kisiler,id',
            'danisman_id' => 'nullable|exists:users,id',
            'il_id' => 'nullable|exists:iller,id',
            'ilce_id' => 'nullable|exists:ilceler,id',
            'mahalle_id' => 'nullable|exists:mahalleler,id',
            'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede',
            'min_konaklama' => 'nullable|integer|min:1|max:365',
            'max_misafir' => 'nullable|integer|min:1|max:50',
            'temizlik_ucreti' => 'nullable|numeric|min:0',
            'havuz' => 'nullable|boolean',
            'havuz_turu' => 'nullable|string|max:100',
            'havuz_boyut' => 'nullable|string|max:50',
            'havuz_derinlik' => 'nullable|string|max:50',
            'havuz_boyut_en' => 'nullable|string|max:20',
            'havuz_boyut_boy' => 'nullable|string|max:20',
            'gunluk_fiyat' => 'nullable|numeric|min:0',
            'haftalik_fiyat' => 'nullable|numeric|min:0',
            'aylik_fiyat' => 'nullable|numeric|min:0',
            'sezonluk_fiyat' => 'nullable|numeric|min:0',
            'sezon_baslangic' => 'nullable|date',
            'sezon_bitis' => 'nullable|date|after_or_equal:sezon_baslangic',
            'elektrik_dahil' => 'nullable|boolean',
            'su_dahil' => 'nullable|boolean',
            'internet_dahil' => 'nullable|boolean',
            'carsaf_dahil' => 'nullable|boolean',
            'havlu_dahil' => 'nullable|boolean',
            'klima_var' => 'nullable|boolean',
            'oda_sayisi' => 'nullable|integer|min:1|max:20',
            'banyo_sayisi' => 'nullable|integer|min:1|max:10',
            'yatak_sayisi' => 'nullable|integer|min:1|max:20',
            'restoran_mesafe' => 'nullable|integer|min:0|max:100',
            'market_mesafe' => 'nullable|integer|min:0|max:100',
            'deniz_mesafe' => 'nullable|integer|min:0|max:100',
            'merkez_mesafe' => 'nullable|integer|min:0|max:100',
            'bahce_var' => 'nullable|boolean',
            'tv_var' => 'nullable|boolean',
            'barbeku_var' => 'nullable|boolean',
            'sezlong_var' => 'nullable|boolean',
            'bahce_masasi_var' => 'nullable|boolean',
            'manzara' => 'nullable|string|max:100',
            'ev_tipi' => 'nullable|string|max:50',
            'ev_konsepti' => 'nullable|string|max:100',
            'proje_id' => 'nullable|exists:projeler,id',
        ];

        $dynamic = (new CategoryFieldValidator)->getRules($kategoriSlug, $yayinTipiSlug);

        return array_merge($base, $dynamic);
    }

    public function withValidator($validator)
    {
        $yayinId = $this->input('yayin_tipi_id');
        $slug = null;
        if ($yayinId) {
            $yayin = \App\Models\IlanKategoriYayinTipi::find($yayinId);
            $slug = $yayin ? strtolower($yayin->slug ?? '') : null;
        }
        if (in_array($slug, ['on-satis', 'insaat-halinde'])) {
            $validator->addRules([
                'proje_id' => 'required|exists:projeler,id',
            ]);
        }
    }
}
