<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\IlanKategoriYayinTipi;
use App\Models\Ilce;
use App\Models\Mahalle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Bodrum Demo Seeder
 *
 * Bodrum bölgesi için gerçekçi demo verileri oluşturur:
 * - 5 adet Arazi (Arsa) - Gerçek ada/parsel numaraları ile
 * - 5 adet Yazlık Kiralık Villa - Detaylı yazlık özellikleri ile
 * - 5 adet Satılık Konut - Villa ve Daire karışımı
 *
 * Context7 Standardı: C7-BODRUM-DEMO-SEEDER-2025-11-07
 * Versiyon: 1.0.0
 */
class BodrumDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏖️ Bodrum Demo Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-BODRUM-DEMO-SEEDER-2025-11-07');
        $this->command->newLine();

        // Schema kontrolü
        if (! Schema::hasTable('ilanlar')) {
            $this->command->error('❌ ilanlar tablosu bulunamadı!');

            return;
        }

        // Transaction içinde çalıştır
        DB::transaction(function () {
            // Kategorileri ve yayın tiplerini yükle
            $this->loadCategoriesAndTypes();

            // Lokasyon verilerini hazırla
            $location = $this->prepareLocation();

            // 1. Arazi (Arsa) verileri
            $this->command->info('🏞️ 1. Arazi (Arsa) verileri oluşturuluyor...');
            $this->seedArsa($location);

            // 2. Yazlık Kiralık Villa verileri
            $this->command->info('🏖️ 2. Yazlık Kiralık Villa verileri oluşturuluyor...');
            $this->seedYazlikVilla($location);

            // 3. Satılık Konut verileri
            $this->command->info('🏠 3. Satılık Konut verileri oluşturuluyor...');
            $this->seedKonut($location);
        });

        $this->command->newLine();
        $this->command->info('✅ Bodrum Demo Seeder başarıyla tamamlandı!');
        $this->command->info('📊 Toplam 15 adet demo ilan oluşturuldu');
    }

    /**
     * Kategorileri ve yayın tiplerini yükle
     */
    private function loadCategoriesAndTypes(): void
    {
        // Ana kategoriler
        $this->kategoriArsa = IlanKategori::where('slug', 'arsa')
            ->orWhere(function ($q) {
                $q->whereNull('parent_id')->where('name', 'Arsa');
            })->first();

        $this->kategoriKonut = IlanKategori::where('slug', 'konut')
            ->orWhere(function ($q) {
                $q->whereNull('parent_id')->where('name', 'Konut');
            })->first();

        $this->kategoriYazlik = IlanKategori::where('slug', 'yazlik')
            ->orWhere(function ($q) {
                $q->whereNull('parent_id')->where('name', 'Yazlık');
            })->first();

        // Alt kategoriler
        $this->altKategoriArsa = $this->kategoriArsa?->children()
            ->where('slug', 'imarli-arsa')
            ->orWhere('name', 'İmarlı Arsa')
            ->first() ?? $this->kategoriArsa?->children()->first();

        $this->altKategoriVilla = $this->kategoriKonut?->children()
            ->where('slug', 'villa')
            ->orWhere('name', 'Villa')
            ->first();

        $this->altKategoriDaire = $this->kategoriKonut?->children()
            ->where('slug', 'daire')
            ->orWhere('name', 'Daire')
            ->first();

        $this->altKategoriYazlikVilla = $this->kategoriYazlik?->children()
            ->where('slug', 'villa')
            ->orWhere('name', 'Villa')
            ->first() ?? $this->kategoriYazlik?->children()->first();

        // Yayın tipleri
        $this->yayinTipiSatilik = IlanKategoriYayinTipi::where('yayin_tipi', 'Satılık')->first();
        $this->yayinTipiYazlik = IlanKategoriYayinTipi::where('yayin_tipi', 'Yazlık Kiralık')
            ->orWhere('yayin_tipi', 'Kiralık')
            ->first();
    }

    /**
     * Lokasyon verilerini hazırla
     */
    private function prepareLocation(): array
    {
        $il = Il::firstOrCreate(
            ['il_adi' => 'Muğla'],
            ['plaka_kodu' => '48']
        );

        $ilce = Ilce::firstOrCreate(
            ['il_id' => $il->id, 'ilce_adi' => 'Bodrum']
        );

        return [
            'il' => $il,
            'ilce' => $ilce,
        ];
    }

    /**
     * Arazi (Arsa) verilerini seed et
     */
    private function seedArsa(array $location): void
    {
        $arsaData = [
            [
                'baslik' => 'Yalıkavak Geriş Mahallesi\'nde Deniz Manzaralı Konut İmarlı Arsa',
                'ada_no' => '123',
                'parsel_no' => '45',
                'imar_statusu' => 'Konut İmarlı',
                'alan_m2' => 450,
                'fiyat' => 15750000,
                'mahalle' => 'Geriş Mh.',
                'aciklama' => 'Yalıkavak\'ın en prestijli bölgelerinden birinde, denize 800 metre mesafede konut imarlı arsa. Gün batımı manzarası, elektrik ve su altyapısı mevcut. Köşe parsel, ifrazlı, parselli. Yatırım için ideal konum.',
                'kaks' => 0.30,
                'taks' => 0.20,
                'gabari' => 7.50,
                'yola_cephe' => true,
                'altyapi_elektrik' => true,
                'altyapi_su' => true,
                'altyapi_dogalgaz' => false,
                'lat' => 37.0583,
                'lng' => 27.2578,
            ],
            [
                'baslik' => 'Bitez Çamlık Sokak\'ta Turizm İmarlı Yatırım Arsası',
                'ada_no' => '89',
                'parsel_no' => '12',
                'imar_statusu' => 'Turizm İmarlı',
                'alan_m2' => 380,
                'fiyat' => 11400000,
                'mahalle' => 'Bitez Mh.',
                'aciklama' => 'Bitez merkeze 500 metre mesafede, turizm imarlı arsa. Otel, pansiyon veya turistik tesis yapımına uygun. Elektrik, su, telefon ve kanalizasyon altyapısı mevcut. Ana yola cepheli.',
                'kaks' => 0.40,
                'taks' => 0.25,
                'gabari' => 8.00,
                'yola_cephe' => true,
                'altyapi_elektrik' => true,
                'altyapi_su' => true,
                'altyapi_dogalgaz' => true,
                'lat' => 37.0333,
                'lng' => 27.2667,
            ],
            [
                'baslik' => 'Gündoğan Kızılburun\'da Zeytinlik İçinde Tarla',
                'ada_no' => '234',
                'parsel_no' => '67',
                'imar_statusu' => 'İmarsız',
                'alan_m2' => 520,
                'fiyat' => 9360000,
                'mahalle' => 'Gündoğan Mh.',
                'aciklama' => 'Gündoğan\'ın sakin bölgesinde, zeytin ağaçlarıyla çevrili tarla. Denize 1.2 km mesafede. Doğal güzellikler içinde, sessiz ve huzurlu bir konum. Elektrik ve su mevcut.',
                'kaks' => null,
                'taks' => null,
                'gabari' => null,
                'yola_cephe' => true,
                'altyapi_elektrik' => true,
                'altyapi_su' => true,
                'altyapi_dogalgaz' => false,
                'lat' => 37.0167,
                'lng' => 27.2833,
            ],
            [
                'baslik' => 'Turgutreis Kadıkalesi\'nde Projeli Arsa Parselleri',
                'ada_no' => '156',
                'parsel_no' => '23',
                'imar_statusu' => 'Konut İmarlı',
                'alan_m2' => 420,
                'fiyat' => 12600000,
                'mahalle' => 'Turgutreis Mh.',
                'aciklama' => 'Turgutreis Kadıkalesi bölgesinde, projeli arsa parseli. Denize 600 metre mesafede, gün batımı manzarası. Elektrik, su, telefon ve kanalizasyon altyapısı tam. Havaalanına 15 km.',
                'kaks' => 0.35,
                'taks' => 0.22,
                'gabari' => 7.80,
                'yola_cephe' => true,
                'altyapi_elektrik' => true,
                'altyapi_su' => true,
                'altyapi_dogalgaz' => true,
                'lat' => 37.0000,
                'lng' => 27.2500,
            ],
            [
                'baslik' => 'Gümüşlük Koyunbaba\'da Gün Batımı Manzaralı Arsa',
                'ada_no' => '78',
                'parsel_no' => '34',
                'imar_statusu' => 'Konut İmarlı',
                'alan_m2' => 480,
                'fiyat' => 14400000,
                'mahalle' => 'Gümüşlük Mh.',
                'aciklama' => 'Gümüşlük\'ün eşsiz gün batımı manzarasına sahip bölgesinde konut imarlı arsa. Denize 400 metre mesafede, doğal güzellikler içinde. Elektrik ve su altyapısı mevcut. İfrazlı parsel.',
                'kaks' => 0.32,
                'taks' => 0.21,
                'gabari' => 7.20,
                'yola_cephe' => true,
                'altyapi_elektrik' => true,
                'altyapi_su' => true,
                'altyapi_dogalgaz' => false,
                'lat' => 37.0500,
                'lng' => 27.2333,
            ],
        ];

        foreach ($arsaData as $data) {
            $mahalle = Mahalle::firstOrCreate(
                ['ilce_id' => $location['ilce']->id, 'mahalle_adi' => $data['mahalle']]
            );

            $ilanData = [
                'baslik' => $data['baslik'],
                'slug' => Str::slug($data['baslik']).'-'.Str::random(6),
                'aciklama' => $data['aciklama'],
                'fiyat' => $data['fiyat'],
                'para_birimi' => 'TRY',
                'status' => 'yayinda',
                'il_id' => $location['il']->id,
                'ilce_id' => $location['ilce']->id,
                'mahalle_id' => $mahalle->id,
                'ana_kategori_id' => $this->kategoriArsa?->id,
                'alt_kategori_id' => $this->altKategoriArsa?->id,
                'yayin_tipi_id' => $this->yayinTipiSatilik?->id,
                // Arsa özel alanlar
                'ada_no' => $data['ada_no'],
                'parsel_no' => $data['parsel_no'],
                'ada_parsel' => $data['ada_no'].'/'.$data['parsel_no'],
                'imar_statusu' => $data['imar_statusu'],
                'alan_m2' => $data['alan_m2'],
                'brut_m2' => $data['alan_m2'],
                'net_m2' => $data['alan_m2'],
                'kaks' => $data['kaks'],
                'taks' => $data['taks'],
                'gabari' => $data['gabari'],
                'yola_cephe' => $data['yola_cephe'],
                'altyapi_elektrik' => $data['altyapi_elektrik'],
                'altyapi_su' => $data['altyapi_su'],
                'altyapi_dogalgaz' => $data['altyapi_dogalgaz'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Ilan::updateOrCreate(
                [
                    'baslik' => $data['baslik'],
                    'il_id' => $location['il']->id,
                    'mahalle_id' => $mahalle->id,
                ],
                $ilanData
            );
        }

        $this->command->info('   ✓ 5 adet arazi oluşturuldu');
    }

    /**
     * Yazlık Kiralık Villa verilerini seed et
     */
    private function seedYazlikVilla(array $location): void
    {
        $yazlikData = [
            [
                'baslik' => 'Yalıkavak Tilkicik Koyu\'nda Infinity Pool Panoramik Deniz Manzaralı Villa',
                'gunluk_fiyat' => 25000,
                'haftalik_fiyat' => 150000,
                'aylik_fiyat' => 550000,
                'brut_m2' => 280,
                'net_m2' => 240,
                'oda_sayisi' => 5,
                'banyo_sayisi' => 4,
                'max_misafir' => 10,
                'min_konaklama' => 7,
                'havuz' => true,
                'havuz_turu' => 'Özel Havuz',
                'havuz_boyut' => '12x6',
                'havuz_derinlik' => 1.80,
                'temizlik_ucreti' => 2000,
                'sezon_baslangic' => '2025-06-01',
                'sezon_bitis' => '2025-09-30',
                'elektrik_dahil' => false,
                'su_dahil' => true,
                'mahalle' => 'Yalıkavak Mh.',
                'aciklama' => 'Yalıkavak\'ın en lüks bölgelerinden birinde, denize 200 metre mesafede infinity pool\'lu villa. 5 yatak odası, 4 banyo, geniş teras, akıllı ev sistemi, kapalı otopark. Maksimum 10 kişi konaklayabilir.',
                'lat' => 37.0583,
                'lng' => 27.2578,
            ],
            [
                'baslik' => 'Gündoğan Sahilde Gün Batımlı Modern Villa',
                'gunluk_fiyat' => 18000,
                'haftalik_fiyat' => 110000,
                'aylik_fiyat' => 420000,
                'brut_m2' => 240,
                'net_m2' => 200,
                'oda_sayisi' => 4,
                'banyo_sayisi' => 3,
                'max_misafir' => 8,
                'min_konaklama' => 5,
                'havuz' => true,
                'havuz_turu' => 'Isıtmalı Havuz',
                'havuz_boyut' => '10x5',
                'havuz_derinlik' => 1.60,
                'temizlik_ucreti' => 1500,
                'sezon_baslangic' => '2025-06-01',
                'sezon_bitis' => '2025-09-30',
                'elektrik_dahil' => false,
                'su_dahil' => true,
                'mahalle' => 'Gündoğan Mh.',
                'aciklama' => 'Gündoğan sahilde, gün batımı manzaralı modern villa. 4 yatak odası, 3 banyo, ısıtmalı havuz, geniş bahçe, teras. Denize 150 metre mesafede. Maksimum 8 kişi konaklayabilir.',
                'lat' => 37.0167,
                'lng' => 27.2833,
            ],
            [
                'baslik' => 'Bitez Beach Loft 3+1 Yazlık Villa',
                'gunluk_fiyat' => 12000,
                'haftalik_fiyat' => 75000,
                'aylik_fiyat' => 280000,
                'brut_m2' => 180,
                'net_m2' => 150,
                'oda_sayisi' => 3,
                'banyo_sayisi' => 2,
                'max_misafir' => 6,
                'min_konaklama' => 3,
                'havuz' => false,
                'havuz_turu' => null,
                'havuz_boyut' => null,
                'havuz_derinlik' => null,
                'temizlik_ucreti' => 1000,
                'sezon_baslangic' => '2025-06-01',
                'sezon_bitis' => '2025-09-30',
                'elektrik_dahil' => true,
                'su_dahil' => true,
                'mahalle' => 'Bitez Mh.',
                'aciklama' => 'Bitez plajına 100 metre mesafede, modern loft tarzı villa. 3 yatak odası, 2 banyo, geniş teras, bahçe. Site havuzu kullanımı dahil. Maksimum 6 kişi konaklayabilir.',
                'lat' => 37.0333,
                'lng' => 27.2667,
            ],
            [
                'baslik' => 'Göltürkbükü Boho Tarzı 5+1 Özel Vadi Villası',
                'gunluk_fiyat' => 22000,
                'haftalik_fiyat' => 130000,
                'aylik_fiyat' => 480000,
                'brut_m2' => 300,
                'net_m2' => 260,
                'oda_sayisi' => 5,
                'banyo_sayisi' => 4,
                'max_misafir' => 12,
                'min_konaklama' => 7,
                'havuz' => true,
                'havuz_turu' => 'Özel Havuz',
                'havuz_boyut' => '14x7',
                'havuz_derinlik' => 1.90,
                'temizlik_ucreti' => 2500,
                'sezon_baslangic' => '2025-06-01',
                'sezon_bitis' => '2025-09-30',
                'elektrik_dahil' => false,
                'su_dahil' => true,
                'mahalle' => 'Göltürkbükü Mh.',
                'aciklama' => 'Göltürkbükü\'nün özel vadilerinden birinde, boho tarzı lüks villa. 5 yatak odası, 4 banyo, geniş bahçe, yoga deck, özel şef hizmeti. Maksimum 12 kişi konaklayabilir.',
                'lat' => 37.0417,
                'lng' => 27.2750,
            ],
            [
                'baslik' => 'Torba Family House - Özel Isıtmalı Havuzlu Yazlık Villa',
                'gunluk_fiyat' => 15000,
                'haftalik_fiyat' => 90000,
                'aylik_fiyat' => 350000,
                'brut_m2' => 220,
                'net_m2' => 190,
                'oda_sayisi' => 4,
                'banyo_sayisi' => 3,
                'max_misafir' => 8,
                'min_konaklama' => 5,
                'havuz' => true,
                'havuz_turu' => 'Isıtmalı Havuz',
                'havuz_boyut' => '11x6',
                'havuz_derinlik' => 1.70,
                'temizlik_ucreti' => 1800,
                'sezon_baslangic' => '2025-06-01',
                'sezon_bitis' => '2025-09-30',
                'elektrik_dahil' => false,
                'su_dahil' => true,
                'mahalle' => 'Torba Mh.',
                'aciklama' => 'Torba\'nın sakin bölgesinde, aileler için ideal villa. 4 yatak odası, 3 banyo, ısıtmalı havuz, çocuk oyun alanı, şömine. Denize 300 metre mesafede. Maksimum 8 kişi konaklayabilir.',
                'lat' => 37.0250,
                'lng' => 27.2417,
            ],
        ];

        foreach ($yazlikData as $data) {
            $mahalle = Mahalle::firstOrCreate(
                ['ilce_id' => $location['ilce']->id, 'mahalle_adi' => $data['mahalle']]
            );

            $ilanData = [
                'baslik' => $data['baslik'],
                'slug' => Str::slug($data['baslik']).'-'.Str::random(6),
                'aciklama' => $data['aciklama'],
                'fiyat' => $data['gunluk_fiyat'], // Ana fiyat günlük fiyat
                'para_birimi' => 'TRY',
                'status' => 'yayinda',
                'il_id' => $location['il']->id,
                'ilce_id' => $location['ilce']->id,
                'mahalle_id' => $mahalle->id,
                'ana_kategori_id' => $this->kategoriYazlik?->id,
                'alt_kategori_id' => $this->altKategoriYazlikVilla?->id,
                'yayin_tipi_id' => $this->yayinTipiYazlik?->id,
                // Yazlık özel alanlar
                'gunluk_fiyat' => $data['gunluk_fiyat'],
                'haftalik_fiyat' => $data['haftalik_fiyat'],
                'aylik_fiyat' => $data['aylik_fiyat'],
                'brut_m2' => $data['brut_m2'],
                'net_m2' => $data['net_m2'],
                'oda_sayisi' => $data['oda_sayisi'],
                'banyo_sayisi' => $data['banyo_sayisi'],
                'max_misafir' => $data['max_misafir'],
                'min_konaklama' => $data['min_konaklama'],
                'havuz' => $data['havuz'],
                'havuz_turu' => $data['havuz_turu'],
                'havuz_boyut' => $data['havuz_boyut'],
                'havuz_derinlik' => $data['havuz_derinlik'],
                'temizlik_ucreti' => $data['temizlik_ucreti'],
                'sezon_baslangic' => $data['sezon_baslangic'],
                'sezon_bitis' => $data['sezon_bitis'],
                'elektrik_dahil' => $data['elektrik_dahil'],
                'su_dahil' => $data['su_dahil'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Ilan::updateOrCreate(
                [
                    'baslik' => $data['baslik'],
                    'il_id' => $location['il']->id,
                    'mahalle_id' => $mahalle->id,
                ],
                $ilanData
            );
        }

        $this->command->info('   ✓ 5 adet yazlık kiralık villa oluşturuldu');
    }

    /**
     * Satılık Konut verilerini seed et
     */
    private function seedKonut(array $location): void
    {
        $konutData = [
            [
                'baslik' => 'Yalıkavak Tilkicik Koyu\'nda Panoramik Deniz Manzaralı Lüks Villa',
                'fiyat' => 42500000,
                'brut_m2' => 320,
                'net_m2' => 280,
                'oda_sayisi' => 5,
                'banyo_sayisi' => 4,
                'salon_sayisi' => 2,
                'kat' => 2,
                'toplam_kat' => 2,
                'bina_yasi' => 2020,
                'isitma' => 'Merkezi Klima',
                'isinma_tipi' => 'VRV Klima',
                'esyali' => true,
                'aidat' => '2500',
                'tip' => 'villa',
                'mahalle' => 'Yalıkavak Mh.',
                'aciklama' => 'Yalıkavak\'ın en prestijli bölgelerinden birinde, denize 250 metre mesafede lüks villa. 5 yatak odası, 4 banyo, 2 salon, geniş teras, özel havuz, kapalı otopark, akıllı ev sistemi. 2020 yılında inşa edilmiş, sıfır bina.',
                'lat' => 37.0583,
                'lng' => 27.2578,
            ],
            [
                'baslik' => 'Türkbükü Sahil Çizgisine 100 m Mesafede Modern Villa',
                'fiyat' => 55000000,
                'brut_m2' => 380,
                'net_m2' => 330,
                'oda_sayisi' => 6,
                'banyo_sayisi' => 5,
                'salon_sayisi' => 2,
                'kat' => 3,
                'toplam_kat' => 3,
                'bina_yasi' => 2022,
                'isitma' => 'Yerden Isıtma',
                'isinma_tipi' => 'Isı Pompası',
                'esyali' => true,
                'aidat' => '3500',
                'tip' => 'villa',
                'mahalle' => 'Göltürkbükü Mh.',
                'aciklama' => 'Türkbükü sahilde, denize 100 metre mesafede modern villa. 6 yatak odası, 5 banyo, 2 salon, ısıtmalı havuz, özel iskele, sauna, asansör. 2022 yılında inşa edilmiş, lüks donanımlı.',
                'lat' => 37.0417,
                'lng' => 27.2750,
            ],
            [
                'baslik' => 'Bodrum Marina Bölgesinde Loft Tarzı Deniz Manzaralı Daire',
                'fiyat' => 18500000,
                'brut_m2' => 160,
                'net_m2' => 130,
                'oda_sayisi' => 2,
                'banyo_sayisi' => 2,
                'salon_sayisi' => 1,
                'kat' => 3,
                'toplam_kat' => 5,
                'bina_yasi' => 2018,
                'isitma' => 'Merkezi Klima',
                'isinma_tipi' => 'Split Klima',
                'esyali' => false,
                'aidat' => '1800',
                'tip' => 'daire',
                'mahalle' => 'Eskiçeşme Mh.',
                'aciklama' => 'Bodrum Marina\'ya 200 metre mesafede, loft tarzı modern daire. 2 yatak odası, 2 banyo, geniş teras, marina ve kale manzarası. Site güvenliği, açık otopark, merkezi klima. 2018 yılında inşa edilmiş.',
                'lat' => 37.0333,
                'lng' => 27.4333,
            ],
            [
                'baslik' => 'Bitez Sarnıç Sokak\'ta Bahçe Kullanımlı Dubleks Daire',
                'fiyat' => 22500000,
                'brut_m2' => 200,
                'net_m2' => 170,
                'oda_sayisi' => 3,
                'banyo_sayisi' => 3,
                'salon_sayisi' => 1,
                'kat' => 1,
                'toplam_kat' => 2,
                'bina_yasi' => 2019,
                'isitma' => 'Kombi',
                'isinma_tipi' => 'Doğalgaz',
                'esyali' => false,
                'aidat' => '2200',
                'tip' => 'daire',
                'mahalle' => 'Bitez Mh.',
                'aciklama' => 'Bitez merkeze 300 metre mesafede, bahçe kullanımlı dubleks daire. 3 yatak odası, 3 banyo, geniş bahçe, teras alanı, kapalı garaj. 2019 yılında inşa edilmiş, bakımlı.',
                'lat' => 37.0333,
                'lng' => 27.2667,
            ],
            [
                'baslik' => 'Ortakent Yahşi\'de Özel Plaja Yakın 5+2 Lüks Villa',
                'fiyat' => 48000000,
                'brut_m2' => 350,
                'net_m2' => 300,
                'oda_sayisi' => 5,
                'banyo_sayisi' => 5,
                'salon_sayisi' => 2,
                'kat' => 2,
                'toplam_kat' => 2,
                'bina_yasi' => 2021,
                'isitma' => 'Yerden Isıtma',
                'isinma_tipi' => 'Isı Pompası',
                'esyali' => true,
                'aidat' => '3000',
                'tip' => 'villa',
                'mahalle' => 'Ortakentyahşi Mh.',
                'aciklama' => 'Ortakent Yahşi\'de özel plaja 150 metre mesafede lüks villa. 5 yatak odası, 5 banyo, 2 salon, sonsuzluk havuzu, asansör, çift mutfak, geniş bahçe. 2021 yılında inşa edilmiş, premium donanımlı.',
                'lat' => 37.0083,
                'lng' => 27.2417,
            ],
        ];

        foreach ($konutData as $data) {
            $mahalle = Mahalle::firstOrCreate(
                ['ilce_id' => $location['ilce']->id, 'mahalle_adi' => $data['mahalle']]
            );

            $altKategoriId = $data['tip'] === 'villa'
                ? $this->altKategoriVilla?->id
                : $this->altKategoriDaire?->id;

            $ilanData = [
                'baslik' => $data['baslik'],
                'slug' => Str::slug($data['baslik']).'-'.Str::random(6),
                'aciklama' => $data['aciklama'],
                'fiyat' => $data['fiyat'],
                'para_birimi' => 'TRY',
                'status' => 'yayinda',
                'il_id' => $location['il']->id,
                'ilce_id' => $location['ilce']->id,
                'mahalle_id' => $mahalle->id,
                'ana_kategori_id' => $this->kategoriKonut?->id,
                'alt_kategori_id' => $altKategoriId,
                'yayin_tipi_id' => $this->yayinTipiSatilik?->id,
                // Konut özel alanlar
                'brut_m2' => $data['brut_m2'],
                'net_m2' => $data['net_m2'],
                'oda_sayisi' => $data['oda_sayisi'],
                'banyo_sayisi' => $data['banyo_sayisi'],
                'salon_sayisi' => $data['salon_sayisi'],
                'kat' => $data['kat'],
                'toplam_kat' => $data['toplam_kat'],
                'bina_yasi' => $data['bina_yasi'],
                'isitma' => $data['isitma'],
                'isinma_tipi' => $data['isinma_tipi'],
                'esyali' => $data['esyali'],
                'aidat' => $data['aidat'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            Ilan::updateOrCreate(
                [
                    'baslik' => $data['baslik'],
                    'il_id' => $location['il']->id,
                    'mahalle_id' => $mahalle->id,
                ],
                $ilanData
            );
        }

        $this->command->info('   ✓ 5 adet satılık konut oluşturuldu');
    }
}
