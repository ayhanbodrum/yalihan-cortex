<?php

namespace App\Services\CRM;

use App\Models\Kisi;
use App\Models\Ilan;

class CRMDataAggregator
{
    public function person(int $id): array
    {
        $k = Kisi::find($id);
        if (!$k) return [];
        return [
            'id' => $k->id,
            'ad' => $k->ad,
            'soyad' => $k->soyad,
            'telefon' => $k->telefon,
            'email' => $k->email,
        ];
    }

    public function listing(int $id): array
    {
        $ilan = Ilan::with(['anaKategori','altKategori','il','ilce'])->find($id);
        if (!$ilan) return [];
        return [
            'id' => $ilan->id,
            'baslik' => $ilan->baslik,
            'kategori' => $ilan->anaKategori?->name,
            'alt_kategori' => $ilan->altKategori?->name,
            'lokasyon' => [
                'il' => $ilan->il?->il_adi,
                'ilce' => $ilan->ilce?->ilce_adi,
            ],
        ];
    }
}