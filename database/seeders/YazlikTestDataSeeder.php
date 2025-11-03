<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ilan;
use App\Models\YazlikDetail;
use Illuminate\Support\Str;

class YazlikTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Yazlık test verileri oluşturuluyor...');

        $ilan = Ilan::first();

        if (!$ilan) {
            $this->command->error('Önce bir ilan oluşturun!');
            return;
        }

        if (YazlikDetail::where('ilan_id', $ilan->id)->exists()) {
            $this->command->warn('Bu ilan için zaten yazlık detayı var');
            return;
        }

        YazlikDetail::create([
            'ilan_id' => $ilan->id,
            'min_konaklama' => 3,
            'max_misafir' => 8,
            'temizlik_ucreti' => 500,
            'havuz' => true,
            'havuz_turu' => 'Özel Havuz',
            'havuz_boyut' => '10x5',
            'havuz_derinlik' => '1.5m',
            'havuz_boyut_en' => '10',
            'havuz_boyut_boy' => '5',
            'gunluk_fiyat' => 5000,
            'haftalik_fiyat' => 30000,
            'aylik_fiyat' => 100000,
            'sezonluk_fiyat' => 360000,
            'sezon_baslangic' => '2025-06-01',
            'sezon_bitis' => '2025-09-30',
            'elektrik_dahil' => true,
            'su_dahil' => true,
            'internet_dahil' => true,
            'carsaf_dahil' => true,
            'havlu_dahil' => true,
            'klima_var' => true,
            'oda_sayisi' => 5,
            'banyo_sayisi' => 3,
            'yatak_sayisi' => 4,
            'yatak_turleri' => ['Çift Kişilik Yatak'],
            'restoran_mesafe' => 500,
            'market_mesafe' => 200,
            'deniz_mesafe' => 100,
            'merkez_mesafe' => 1000,
            'bahce_var' => true,
            'tv_var' => true,
            'barbeku_var' => true,
            'sezlong_var' => true,
            'bahce_masasi_var' => true,
            'manzara' => 'Deniz Manzaralı',
            'ozel_isaretler' => ['Pet Kabul Edilir', 'Sigaraya Kapalı'],
            'ev_tipi' => 'Villa',
            'ev_konsepti' => 'Lüks Tatil',
            'ozel_notlar' => 'Check-in: 14:00, Check-out: 11:00',
            'musteri_notlari' => 'Denize giriş noktası yazlığın önündedir.',
            'indirim_notlari' => 'Uzun dönem kiralarda özel fiyat',
            'anahtar_kimde' => 'Site Güvenliği',
            'eids_onayli' => true,
            'eids_onay_tarihi' => now(),
        ]);

        $this->command->info('Yazlık detayları başarıyla eklendi!');
        $this->command->info('İlan ID: ' . $ilan->id);
        $this->command->info('Başlık: ' . $ilan->baslik);
    }
}
