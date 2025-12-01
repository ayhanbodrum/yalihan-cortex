<?php

namespace Database\Seeders;

use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanFotografi;
use App\Models\IlanKategori;
use App\Models\Ilce; // Eski Il modeli yerine Sehir modeli kullanılıyor
use App\Models\Kisi;
use App\Models\Mahalle;
use App\Models\Talep;
use App\Models\Ulke;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BodrumSeeder extends Seeder
{
    /**
     * Bodrum bölgesi için örnek veriler oluşturur.
     */
    public function run(): void
    {
        $faker = Faker::create('tr_TR');
        $this->command->info('Bodrum bolgesi icin ornek veriler olusturuluyor...');

        // Türkiye'yi bul
        $turkiye = Ulke::where('ulke_kodu', 'TR')->first();
        if (! $turkiye) {
            $this->command->error('Turkiye ulkesi bulunamadi! Lutfen once UlkelerTableSeeder calistirin.');

            return;
        }

        // Muğla'yı bul veya oluştur (yalnızca mevcut kolonlara göre)
        $mugla = Il::firstOrCreate(
            ['il_adi' => 'Muğla'],
            []
        );
        $this->command->info('Mugla sehri bulundu veya olusturuldu.');

        // Bodrum'u bul veya oluştur
        $bodrum = Ilce::firstOrCreate(
            ['ilce_adi' => 'Bodrum', 'il_id' => $mugla->id],
            ['status' => true]
        );
        $this->command->info('Bodrum ilcesi bulundu veya olusturuldu.');

        // Bodrum'daki mahalleler
        $mahalleNames = [
            'Gümbet', 'Bitez', 'Ortakent', 'Yalıkavak', 'Gündoğan', 'Türkbükü',
            'Göltürkbükü', 'Torba', 'Gümüşlük', 'Turgutreis', 'Konacık',
            'Yalı', 'Kumbahçe', 'Eskiçeşme', 'Umurça',
        ];

        $mahalleler = [];
        foreach ($mahalleNames as $mahalleName) {
            $mahalleler[] = Mahalle::updateOrCreate(
                ['ilce_id' => $bodrum->id, 'mahalle_adi' => $mahalleName],
                [
                    'mahalle_kodu' => 'BDR-'.strtoupper(substr($mahalleName, 0, 3)).rand(100, 999),
                ]
            );
        }
        $this->command->info(count($mahalleler).' adet mahalle veritabanina eklendi.');

        // Rol bilgilerini al
        $this->command->info('Kullanıcılar oluşturuluyor...');

        // Rol bilgilerini al veya oluştur
        $danismanRole = DB::table('roles')->where('name', 'danisman')->first();
        $editorRole = DB::table('roles')->where('name', 'editor')->first();

        if (! $danismanRole) {
            // Danışman rolü yoksa oluştur
            $danismanRoleId = DB::table('roles')->insertGetId([
                'name' => 'danisman',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Danışman rolü oluşturuldu.');
        } else {
            $danismanRoleId = $danismanRole->id;
        }

        if (! $editorRole) {
            // Editor rolü yoksa oluştur
            $editorRoleId = DB::table('roles')->insertGetId([
                'name' => 'editor',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info('Editor rolü oluşturuldu.');
        } else {
            $editorRoleId = $editorRole->id;
        }

        // Danışman kullanıcıları oluştur
        $danismanlar = [];

        // 1. Yunus Emre Gök
        $danisman1 = $this->createUserWithRole(
            'Yunus Emre Gök',
            'y.emreyalihan@gmail.com',
            '+905342036964',
            $danismanRoleId
        );
        $danismanlar[] = $danisman1;

        // 2. Atılay Önen
        $danisman2 = $this->createUserWithRole(
            'Atılay Önen',
            'atilay.onenn@gmail.com',
            '+905532897039',
            $danismanRoleId
        );
        $danismanlar[] = $danisman2;

        // 3. Ayhan Küçük
        $danisman3 = $this->createUserWithRole(
            'Ayhan Küçük',
            'yalihanemlak@gmail.com',
            '+905332090302',
            $danismanRoleId
        );
        $danismanlar[] = $danisman3;

        // Editor kullanıcısı oluştur
        $editor = $this->createUserWithRole(
            'Yeliz Tan Küçük',
            'yeliztankucuk@gmail.com',
            '+905399553343',
            $editorRoleId
        );

        // Admin ve User demo hesapları oluştur
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $userRole = DB::table('roles')->where('name', 'user')->first();
        $adminRoleId = $adminRole ? $adminRole->id : DB::table('roles')->insertGetId([
            'name' => 'admin',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $userRoleId = $userRole ? $userRole->id : DB::table('roles')->insertGetId([
            'name' => 'user',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createUserWithRole(
            'Admin Demo',
            'admin@example.com',
            '+900000000000',
            $adminRoleId
        );
        $this->createUserWithRole(
            'User Demo',
            'user@example.com',
            '+900000000001',
            $userRoleId
        );

        $danismanlar = collect($danismanlar);

        // İlan kategorilerini al
        $konutKategori = IlanKategori::where('name', 'Konut')->first();
        $isyeriKategori = IlanKategori::where('name', 'İş Yeri')->first();
        $arsaKategori = IlanKategori::where('name', 'Arsa')->first();

        if (! $konutKategori) {
            $this->command->error('Konut kategorisi bulunamadı!');

            return;
        }

        if (! $isyeriKategori) {
            $this->command->error('İş Yeri kategorisi bulunamadı!');

            return;
        }

        if (! $arsaKategori) {
            $this->command->error('Arsa kategorisi bulunamadı!');

            return;
        }

        $this->command->info('Kategori bilgileri alındı.');

        // Konut alt kategorileri
        $konutAltKategoriler = IlanKategori::where('parent_id', $konutKategori->id)->pluck('id')->toArray();
        if (empty($konutAltKategoriler)) {
            $this->command->error('Konut alt kategorileri bulunamadı!');

            return;
        }

        // İşyeri alt kategorileri
        $isyeriAltKategoriler = IlanKategori::where('parent_id', $isyeriKategori->id)->pluck('id')->toArray();
        if (empty($isyeriAltKategoriler)) {
            $this->command->warn('İşyeri için alt kategori bulunamadı, seeder atlanıyor.');
        }

        // Örnek Kişiler (Müşteriler) Oluştur
        $this->command->info('Örnek kişiler (müşteriler) oluşturuluyor...');
        $kisiler = [];
        for ($i = 0; $i < 20; $i++) {
            $secilenMahalle = $mahalleler[array_rand($mahalleler)];
            $kisiler[] = Kisi::create([
                'ad' => $faker->firstName,
                'soyad' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'telefon' => $faker->unique()->e164PhoneNumber,
                'ulke_id' => $turkiye->id,
                'il_id' => $mugla->id,
                'ilce_id' => $bodrum->id,
                'mahalle_id' => $secilenMahalle->id,
                'notlar' => 'BodrumSeeder tarafından oluşturuldu.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info(count($kisiler).' adet örnek kişi oluşturuldu.');

        // Örnek İlanlar Oluştur
        $this->command->info('Örnek ilanlar oluşturuluyor...');
        $ilanlar = [];
        $ilanStatus = ['Yayında', 'Yayından Kaldırıldı', 'Satıldı', 'Kiralandı', 'Beklemede'];
        $ilanTipleri = ['Satılık', 'Kiralık', 'Günlük Kiralık'];

        for ($i = 0; $i < 50; $i++) {
            $secilenMahalle = $mahalleler[array_rand($mahalleler)];
            $secilenDanisman = $danismanlar->random();
            $secilenKisi = $kisiler[array_rand($kisiler)];
            $kategori = rand(0, 1) ? $konutKategori : $isyeriKategori;
            $altKategoriler = $kategori->id == $konutKategori->id ? $konutAltKategoriler : $isyeriAltKategoriler;

            if (empty($altKategoriler)) {
                continue;
            }

            $ilan_basligi = ucfirst($secilenMahalle->mahalle_adi).' Mah. '.$faker->randomElement(['Satılık', 'Kiralık']).' '.$faker->randomElement(['Villa', 'Daire', 'İş Yeri']);

            $ilan = Ilan::create([
                'ilan_basligi' => $ilan_basligi,
                'slug' => Str::slug($ilan_basligi.'-'.uniqid()),
                'ilan_aciklamasi' => $faker->paragraph(5),
                'yayinlama_tipi' => $ilanTipleri[array_rand($ilanTipleri)],
                'fiyat' => $faker->numberBetween(500000, 10000000),
                'para_birimi' => 'TRY',
                'ulke_id' => $turkiye->id,
                'il_id' => $mugla->id,
                'ilce_id' => $bodrum->id,
                'mahalle_id' => $secilenMahalle->id,
                'status' => $ilanStatus[array_rand($ilanStatus)],
                'category_id' => $altKategoriler[array_rand($altKategoriler)],
                'user_id' => $secilenDanisman->id,
                'kisi_id' => $secilenKisi->id,
                'oda_sayisi' => $faker->randomElement(['1+1', '2+1', '3+1', '4+1', '5+1', '6+2', '8+1']),
                'banyo_sayisi' => $faker->numberBetween(1, 5),
                'net_metrekare' => $faker->numberBetween(50, 500),
                'brut_metrekare' => $faker->numberBetween(60, 600),
                'bina_yasi' => $faker->numberBetween(0, 30),
                'kat_sayisi' => $faker->numberBetween(1, 20),
                'bulundugu_kat' => $faker->numberBetween(1, 20),
                'isitma_tipi' => $faker->randomElement(['Klima', 'Merkezi', 'Yerden Isıtma']),
                'tapu_statusu' => $faker->randomElement(['Müstakil Tapu', 'Hisseli Tapu', 'Kat İrtifakı']),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'fiyat_guncelleme_tarihi' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);

            // Örnek ilan fotoğrafları ekle
            for ($j = 0; $j < 5; $j++) {
                IlanFotografi::create([
                    'ilan_id' => $ilan->id,
                    'dosya_yolu' => 'https://picsum.photos/seed/'.rand(1, 1000).'/800/600',
                    'sira' => $j + 1,
                ]);
            }
            $ilanlar[] = $ilan;
        }
        $this->command->info(count($ilanlar).' adet örnek ilan oluşturuldu.');

        // Örnek Talepler Oluştur
        $this->command->info('Örnek talepler oluşturuluyor...');
        $talepTipleri = ['Alım', 'Kiralama'];
        $talepStatus = ['Aktif', 'Pasif', 'Sonuçlandı'];

        for ($i = 0; $i < 30; $i++) {
            $secilenMahalle = $mahalleler[array_rand($mahalleler)];
            $secilenKisi = $kisiler[array_rand($kisiler)];
            $secilenDanisman = $danismanlar->random();
            $kategori = rand(0, 1) ? $konutKategori : $isyeriKategori;
            $altKategoriler = $kategori->id == $konutKategori->id ? $konutAltKategoriler : $isyeriAltKategoriler;

            if (empty($altKategoriler)) {
                continue;
            }

            Talep::create([
                'kisi_id' => $secilenKisi->id,
                'user_id' => $secilenDanisman->id,
                'talep_tipi' => $talepTipleri[array_rand($talepTipleri)],
                'status' => $talepStatus[array_rand($talepStatus)],
                'aciklama' => 'Bodrum '.$secilenMahalle->mahalle_adi.' civarında '.$faker->randomElement(['2+1', '3+1 bahçeli', 'müstakil havuzlu']).' ev aranıyor.',
                'category_id' => $altKategoriler[array_rand($altKategoriler)],
                'ulke_id' => $turkiye->id,
                'il_id' => $mugla->id,
                'ilce_id' => $bodrum->id,
                'mahalle_id' => $secilenMahalle->id,
                'min_fiyat' => $faker->numberBetween(100000, 2000000),
                'max_fiyat' => $faker->numberBetween(2000000, 15000000),
                'min_metrekare' => $faker->numberBetween(80, 150),
                'max_metrekare' => $faker->numberBetween(150, 500),
                'created_at' => Carbon::now()->subDays(rand(0, 180)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);
        }
        $this->command->info('30 adet örnek talep oluşturuldu.');

        $this->command->info('BodrumSeeder başarıyla tamamlandı.');
    }

    /**
     * Belirtilen rol ile bir kullanıcı oluşturur ve rolünü atar.
     * Ayrıca bu kullanıcı için bir Kişi kaydı oluşturur.
     */
    private function createUserWithRole(string $name, string $email, string $phone, int $roleId): User
    {
        // Önce Kişi kaydını oluştur veya güncelle
        $kisi = Kisi::updateOrCreate(
            ['email' => $email],
            [
                'ad' => strtok($name, ' '),
                'soyad' => substr(strstr($name, ' '), 1),
                'telefon' => $phone,
                'ulke_id' => Ulke::where('ulke_kodu', 'TR')->first()->id, // Varsayılan Türkiye
                'il_id' => null, // Danışmanlar genel olabilir
                'ilce_id' => null,
                'mahalle_id' => null,
            ]
        );

        // Sonra User kaydını oluştur veya güncelle
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
                'role_id' => $roleId,
            ]
        );

        // Kişi kaydını User'a bağla (user_id'yi guncelle)
        if ($kisi->user_id !== $user->id) {
            $kisi->user_id = $user->id;
            $kisi->save();
        }

        // Rolü ata
        DB::table('model_has_roles')->updateOrInsert(
            ['model_id' => $user->id, 'model_type' => User::class],
            ['role_id' => $roleId]
        );

        $this->command->info($name.' kullanıcısı ve kişi profili oluşturuldu/güncellendi ve rolü atandı.');

        return $user;
    }
}
