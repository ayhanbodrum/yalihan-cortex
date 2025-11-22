<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Ulke;
use App\Models\Il;

class GoldenVisaPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $villaKategori = IlanKategori::where('slug', 'villa')->orWhere('name', 'Villa')->first();
        $konutKategori = IlanKategori::where('slug', 'konut')->orWhere('name', 'Konut')->first();
        if (! $villaKategori && ! $konutKategori) {
            return;
        }

        $countries = [
            ['code' => 'TR', 'city' => 'Muğla', 'title' => 'Türkiye Golden Visa Uygun Lüks Villa', 'price' => 450000, 'currency' => 'USD'],
            ['code' => 'GR', 'city' => 'Athens', 'title' => 'Yunanistan Golden Visa Uygun Modern Daire', 'price' => 300000, 'currency' => 'EUR'],
            ['code' => 'AE', 'city' => 'Dubai', 'title' => 'BAE Golden Visa Uygun Marina Residence', 'price' => 600000, 'currency' => 'USD'],
            ['code' => 'TR', 'city' => 'İstanbul', 'title' => 'Türkiye Golden Visa Prestijli Konut', 'price' => 420000, 'currency' => 'USD'],
            ['code' => 'GR', 'city' => 'Thessaloniki', 'title' => 'Yunanistan Golden Visa Şehir Merkezi Daire', 'price' => 280000, 'currency' => 'EUR'],
            ['code' => 'AE', 'city' => 'Abu Dhabi', 'title' => 'BAE Golden Visa Premium Residence', 'price' => 700000, 'currency' => 'USD'],
        ];

        foreach ($countries as $i => $c) {
            $ulke = Ulke::where('ulke_kodu', $c['code'])->first();
            $il = Il::where('il_adi', $c['city'])->first();
            $kategoriId = $villaKategori ? $villaKategori->id : $konutKategori->id;
            $title = $c['title'];
            $data = [
                'baslik' => $title,
                'slug' => \Illuminate\Support\Str::slug($title) . '-' . $i,
                'aciklama' => 'Vatandaşlık programına uygun nitelikte, yüksek kira getirili portföy.',
                'fiyat' => $c['price'],
                'para_birimi' => $c['currency'],
                'status' => 'Aktif',
                'ana_kategori_id' => $kategoriId,
                'net_m2' => 95 + (($i % 3) * 10),
                'oda_sayisi' => 3 + ($i % 2),
                'banyo_sayisi' => 2,
            ];
            if ($il) {
                $data['il_id'] = $il->id;
            }
            if ($ulke && \Illuminate\Support\Facades\Schema::hasColumn('ilanlar', 'ulke_id')) {
                $data['ulke_id'] = $ulke->id;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('ilanlar', 'citizenship_eligible')) {
                $data['citizenship_eligible'] = true;
            }
            $ilan = Ilan::create($data);

            $this->attachPhoto($ilan->id, 'screenshot-mobile');
        }
    }

    protected function attachPhoto(int $ilanId, string $source): void
    {
        $publicPath = public_path('images/' . $source . '.png');
        if (! file_exists($publicPath)) {
            return;
        }
        $target = 'seed/gv-' . $source . '-' . $ilanId . '.png';
        Storage::disk('public')->put($target, file_get_contents($publicPath));
        DB::table('ilan_fotograflari')->insert([
            'ilan_id' => $ilanId,
            'dosya_adi' => 'gv-' . $source . '-' . $ilanId . '.png',
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