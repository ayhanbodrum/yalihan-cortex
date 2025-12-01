<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Ilce;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BodrumArsaYazlikSeeder extends Seeder
{
    public function run(): void
    {
        $il = Il::where('il_adi', 'Muğla')->first();
        if (! $il) {
            return;
        }
        $ilce = Ilce::where('ilce_adi', 'Bodrum')->first();
        $arsaKategori = IlanKategori::where('slug', 'arsa')->orWhere('name', 'Arsa')->first();
        $yazlikKategori = IlanKategori::where('slug', 'yazlik')->orWhere('name', 'Yazlık')->first();
        if (! $arsaKategori || ! $yazlikKategori) {
            return;
        }

        $this->seedArsa($il->id, $ilce ? $ilce->id : null, $arsaKategori->id);
        $this->seedYazlik($il->id, $ilce ? $ilce->id : null, $yazlikKategori->id);
    }

    protected function seedArsa(int $ilId, ?int $ilceId, int $kategoriId): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $title = 'Bodrum '.$i.' Parsel Deniz Manzaralı Arsa';
            $ilan = Ilan::create([
                'baslik' => $title,
                'slug' => \Illuminate\Support\Str::slug($title).'-'.$i,
                'aciklama' => 'Denize yakın konumda, imar durumu uygun, yatırım fırsatı.',
                'fiyat' => 4500000 + ($i * 150000),
                'para_birimi' => 'TRY',
                'status' => 'Aktif',
                'il_id' => $ilId,
                'ilce_id' => $ilceId,
                'ana_kategori_id' => $kategoriId,
                'alan_m2' => 600 + ($i * 50),
                'imar_statusu' => 'Konut',
                'ada_no' => (string) (100 + $i),
                'parsel_no' => (string) (200 + $i),
            ]);

            $this->attachPhoto($ilan->id, 'map');
        }
    }

    protected function seedYazlik(int $ilId, ?int $ilceId, int $kategoriId): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $title = 'Bodrum '.$i.' Özel Havuzlu Yazlık Kiralık';
            $ilan = Ilan::create([
                'baslik' => $title,
                'slug' => \Illuminate\Support\Str::slug($title).'-'.$i,
                'aciklama' => 'Merkeze yakın, denize erişimli, ailelere uygun konaklama.',
                'fiyat' => 12500 + ($i * 500),
                'para_birimi' => 'TRY',
                'status' => 'Aktif',
                'il_id' => $ilId,
                'ilce_id' => $ilceId,
                'ana_kategori_id' => $kategoriId,
                'gunluk_fiyat' => 2500 + ($i * 100),
                'haftalik_fiyat' => 17500 + ($i * 700),
                'sezonluk_fiyat' => 220000 + ($i * 5000),
                'havuz' => 1,
                'min_konaklama' => 3,
                'max_misafir' => 6 + ($i % 3),
                'net_m2' => 110 + ($i * 5),
                'oda_sayisi' => 3 + ($i % 2),
                'banyo_sayisi' => 2,
            ]);

            $this->attachPhoto($ilan->id, 'screenshot-desktop');
        }
    }

    protected function attachPhoto(int $ilanId, string $source): void
    {
        $publicPath = public_path('images/'.$source.'.png');
        if (! file_exists($publicPath)) {
            return;
        }
        $target = 'seed/'.$source.'-'.$ilanId.'.png';
        Storage::disk('public')->put($target, file_get_contents($publicPath));
        DB::table('ilan_fotograflari')->insert([
            'ilan_id' => $ilanId,
            'dosya_adi' => $source.'-'.$ilanId.'.png',
            'dosya_yolu' => $target,
            'dosya_boyutu' => null,
            'mime_type' => 'image/png',
            'kapak_fotografi' => true,
            'sira' => 0,
            'aciklama' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
