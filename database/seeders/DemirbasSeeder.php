<?php

namespace Database\Seeders;

use App\Models\Demirbas;
use App\Models\DemirbasKategori;
use Illuminate\Database\Seeder;

class DemirbasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * ✅ Context7: Hiyerarşik demirbaşlar oluştur
     */
    public function run(): void
    {
        // Mutfak > Beyaz Eşyalar kategorisini bul
        $mutfakBeyazEsyalar = DemirbasKategori::where('slug', 'mutfak-beyaz-esyalar')->first();
        if ($mutfakBeyazEsyalar) {
            // Beyaz Eşyalar
            Demirbas::create([
                'name' => 'Buzdolabı',
                'slug' => 'buzdolabi',
                'icon' => '❄️',
                'kategori_id' => $mutfakBeyazEsyalar->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Çamaşır Makinesi',
                'slug' => 'camasir-makinesi',
                'icon' => '🌀',
                'kategori_id' => $mutfakBeyazEsyalar->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Bulaşık Makinesi',
                'slug' => 'bulasik-makinesi',
                'icon' => '🍽️',
                'kategori_id' => $mutfakBeyazEsyalar->id,
                'display_order' => 3,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Fırın',
                'slug' => 'firin',
                'icon' => '🔥',
                'kategori_id' => $mutfakBeyazEsyalar->id,
                'display_order' => 4,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Ankastre Set',
                'slug' => 'ankastre-set',
                'icon' => '⚡',
                'kategori_id' => $mutfakBeyazEsyalar->id,
                'display_order' => 5,
                'status' => true,
            ]);
        }

        // Mutfak > Küçük Ev Aletleri kategorisini bul
        $mutfakKucukAletler = DemirbasKategori::where('slug', 'mutfak-kucuk-ev-aletleri')->first();
        if ($mutfakKucukAletler) {
            Demirbas::create([
                'name' => 'Kahve Makinesi',
                'slug' => 'kahve-makinesi',
                'icon' => '☕',
                'kategori_id' => $mutfakKucukAletler->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Mikrodalga Fırın',
                'slug' => 'mikrodalga-firin',
                'icon' => '📡',
                'kategori_id' => $mutfakKucukAletler->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Blender',
                'slug' => 'blender',
                'icon' => '🌀',
                'kategori_id' => $mutfakKucukAletler->id,
                'display_order' => 3,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Tost Makinesi',
                'slug' => 'tost-makinesi',
                'icon' => '🍞',
                'kategori_id' => $mutfakKucukAletler->id,
                'display_order' => 4,
                'status' => true,
            ]);
        }

        // Banyo > Banyo Aksesuarları
        $banyoAksesuarlari = DemirbasKategori::where('slug', 'banyo-aksesuarlari')->first();
        if ($banyoAksesuarlari) {
            Demirbas::create([
                'name' => 'Duşakabin',
                'slug' => 'dusakabin',
                'icon' => '🚿',
                'kategori_id' => $banyoAksesuarlari->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Klozet',
                'slug' => 'klozet',
                'icon' => '🚽',
                'kategori_id' => $banyoAksesuarlari->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Lavabo',
                'slug' => 'lavabo',
                'icon' => '🚰',
                'kategori_id' => $banyoAksesuarlari->id,
                'display_order' => 3,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Banyo Dolabı',
                'slug' => 'banyo-dolabi',
                'icon' => '🪞',
                'kategori_id' => $banyoAksesuarlari->id,
                'display_order' => 4,
                'status' => true,
            ]);
        }

        // Salon > Oturma Grubu
        $salonOturmaGrubu = DemirbasKategori::where('slug', 'salon-oturma-grubu')->first();
        if ($salonOturmaGrubu) {
            Demirbas::create([
                'name' => 'Kanepe Takımı',
                'slug' => 'kanepe-takimi',
                'icon' => '🛋️',
                'kategori_id' => $salonOturmaGrubu->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Koltuk Takımı',
                'slug' => 'koltuk-takimi',
                'icon' => '🪑',
                'kategori_id' => $salonOturmaGrubu->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Sehpa',
                'slug' => 'sehpa',
                'icon' => '🪑',
                'kategori_id' => $salonOturmaGrubu->id,
                'display_order' => 3,
                'status' => true,
            ]);
        }

        // Salon > TV ve Elektronik
        $salonTv = DemirbasKategori::where('slug', 'salon-tv-elektronik')->first();
        if ($salonTv) {
            Demirbas::create([
                'name' => 'TV',
                'slug' => 'tv',
                'icon' => '📺',
                'kategori_id' => $salonTv->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'TV Ünitesi',
                'slug' => 'tv-unitesi',
                'icon' => '📺',
                'kategori_id' => $salonTv->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Ses Sistemi',
                'slug' => 'ses-sistemi',
                'icon' => '🔊',
                'kategori_id' => $salonTv->id,
                'display_order' => 3,
                'status' => true,
            ]);
        }

        // Yatak Odası > Yatak ve Yatak Takımları
        $yatakTakimlari = DemirbasKategori::where('slug', 'yatak-odasi-yatak-takimlari')->first();
        if ($yatakTakimlari) {
            Demirbas::create([
                'name' => 'Yatak',
                'slug' => 'yatak',
                'icon' => '🛏️',
                'kategori_id' => $yatakTakimlari->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Yatak Başlığı',
                'slug' => 'yatak-basligi',
                'icon' => '🛏️',
                'kategori_id' => $yatakTakimlari->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Yatak Takımı',
                'slug' => 'yatak-takimi',
                'icon' => '🛏️',
                'kategori_id' => $yatakTakimlari->id,
                'display_order' => 3,
                'status' => true,
            ]);
        }

        // Yatak Odası > Dolap ve Depolama
        $yatakDolap = DemirbasKategori::where('slug', 'yatak-odasi-dolap-depolama')->first();
        if ($yatakDolap) {
            Demirbas::create([
                'name' => 'Gardırop',
                'slug' => 'gardirob',
                'icon' => '🚪',
                'kategori_id' => $yatakDolap->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Kommode',
                'slug' => 'kommode',
                'icon' => '🚪',
                'kategori_id' => $yatakDolap->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Şifonyer',
                'slug' => 'sifonyer',
                'icon' => '🚪',
                'kategori_id' => $yatakDolap->id,
                'display_order' => 3,
                'status' => true,
            ]);
        }

        // Bahçe > Bahçe Mobilyaları
        $bahceMobilya = DemirbasKategori::where('slug', 'bahce-mobilyalari')->first();
        if ($bahceMobilya) {
            Demirbas::create([
                'name' => 'Bahçe Masası',
                'slug' => 'bahce-masasi',
                'icon' => '🪑',
                'kategori_id' => $bahceMobilya->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Bahçe Sandalyeleri',
                'slug' => 'bahce-sandalyeleri',
                'icon' => '🪑',
                'kategori_id' => $bahceMobilya->id,
                'display_order' => 2,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Şezlong',
                'slug' => 'sezlong',
                'icon' => '🪑',
                'kategori_id' => $bahceMobilya->id,
                'display_order' => 3,
                'status' => true,
            ]);
        }

        // Bahçe > Bahçe Ekipmanları
        $bahceEkipman = DemirbasKategori::where('slug', 'bahce-ekipmanlari')->first();
        if ($bahceEkipman) {
            Demirbas::create([
                'name' => 'Mangal',
                'slug' => 'mangal',
                'icon' => '🔥',
                'kategori_id' => $bahceEkipman->id,
                'display_order' => 1,
                'status' => true,
            ]);

            Demirbas::create([
                'name' => 'Bahçe Şemsiyesi',
                'slug' => 'bahce-semsiyesi',
                'icon' => '☂️',
                'kategori_id' => $bahceEkipman->id,
                'display_order' => 2,
                'status' => true,
            ]);
        }

        $this->command->info('✅ Demirbaşlar oluşturuldu: '.Demirbas::count().' adet');
    }
}
