<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurkiyeIlleriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏛️ Türkiye illeri ekleniyor...');

        // Context7: country_id (NOT ulke_id)
        $provinces = [
            ['country_id' => 1, 'il_adi' => 'Adana', 'plaka_kodu' => '01', 'telefon_kodu' => '322'],
            ['country_id' => 1, 'il_adi' => 'Adıyaman', 'plaka_kodu' => '02', 'telefon_kodu' => '416'],
            ['country_id' => 1, 'il_adi' => 'Afyonkarahisar', 'plaka_kodu' => '03', 'telefon_kodu' => '272'],
            ['country_id' => 1, 'il_adi' => 'Ağrı', 'plaka_kodu' => '04', 'telefon_kodu' => '472'],
            ['country_id' => 1, 'il_adi' => 'Amasya', 'plaka_kodu' => '05', 'telefon_kodu' => '358'],
            ['country_id' => 1, 'il_adi' => 'Ankara', 'plaka_kodu' => '06', 'telefon_kodu' => '312'],
            ['country_id' => 1, 'il_adi' => 'Antalya', 'plaka_kodu' => '07', 'telefon_kodu' => '242'],
            ['country_id' => 1, 'il_adi' => 'Artvin', 'plaka_kodu' => '08', 'telefon_kodu' => '466'],
            ['country_id' => 1, 'il_adi' => 'Aydın', 'plaka_kodu' => '09', 'telefon_kodu' => '256'],
            ['country_id' => 1, 'il_adi' => 'Balıkesir', 'plaka_kodu' => '10', 'telefon_kodu' => '266'],
            ['country_id' => 1, 'il_adi' => 'Bilecik', 'plaka_kodu' => '11', 'telefon_kodu' => '228'],
            ['country_id' => 1, 'il_adi' => 'Bingöl', 'plaka_kodu' => '12', 'telefon_kodu' => '426'],
            ['country_id' => 1, 'il_adi' => 'Bitlis', 'plaka_kodu' => '13', 'telefon_kodu' => '434'],
            ['country_id' => 1, 'il_adi' => 'Bolu', 'plaka_kodu' => '14', 'telefon_kodu' => '374'],
            ['country_id' => 1, 'il_adi' => 'Burdur', 'plaka_kodu' => '15', 'telefon_kodu' => '248'],
            ['country_id' => 1, 'il_adi' => 'Bursa', 'plaka_kodu' => '16', 'telefon_kodu' => '224'],
            ['country_id' => 1, 'il_adi' => 'Çanakkale', 'plaka_kodu' => '17', 'telefon_kodu' => '286'],
            ['country_id' => 1, 'il_adi' => 'Çankırı', 'plaka_kodu' => '18', 'telefon_kodu' => '376'],
            ['country_id' => 1, 'il_adi' => 'Çorum', 'plaka_kodu' => '19', 'telefon_kodu' => '364'],
            ['country_id' => 1, 'il_adi' => 'Denizli', 'plaka_kodu' => '20', 'telefon_kodu' => '258'],
            ['country_id' => 1, 'il_adi' => 'Diyarbakır', 'plaka_kodu' => '21', 'telefon_kodu' => '412'],
            ['country_id' => 1, 'il_adi' => 'Edirne', 'plaka_kodu' => '22', 'telefon_kodu' => '284'],
            ['country_id' => 1, 'il_adi' => 'Elazığ', 'plaka_kodu' => '23', 'telefon_kodu' => '424'],
            ['country_id' => 1, 'il_adi' => 'Erzincan', 'plaka_kodu' => '24', 'telefon_kodu' => '446'],
            ['country_id' => 1, 'il_adi' => 'Erzurum', 'plaka_kodu' => '25', 'telefon_kodu' => '442'],
            ['country_id' => 1, 'il_adi' => 'Eskişehir', 'plaka_kodu' => '26', 'telefon_kodu' => '222'],
            ['country_id' => 1, 'il_adi' => 'Gaziantep', 'plaka_kodu' => '27', 'telefon_kodu' => '342'],
            ['country_id' => 1, 'il_adi' => 'Giresun', 'plaka_kodu' => '28', 'telefon_kodu' => '454'],
            ['country_id' => 1, 'il_adi' => 'Gümüşhane', 'plaka_kodu' => '29', 'telefon_kodu' => '456'],
            ['country_id' => 1, 'il_adi' => 'Hakkari', 'plaka_kodu' => '30', 'telefon_kodu' => '438'],
            ['country_id' => 1, 'il_adi' => 'Hatay', 'plaka_kodu' => '31', 'telefon_kodu' => '326'],
            ['country_id' => 1, 'il_adi' => 'Isparta', 'plaka_kodu' => '32', 'telefon_kodu' => '246'],
            ['country_id' => 1, 'il_adi' => 'Mersin', 'plaka_kodu' => '33', 'telefon_kodu' => '324'],
            ['country_id' => 1, 'il_adi' => 'İstanbul', 'plaka_kodu' => '34', 'telefon_kodu' => '212'],
            ['country_id' => 1, 'il_adi' => 'İzmir', 'plaka_kodu' => '35', 'telefon_kodu' => '232'],
            ['country_id' => 1, 'il_adi' => 'Kars', 'plaka_kodu' => '36', 'telefon_kodu' => '474'],
            ['country_id' => 1, 'il_adi' => 'Kastamonu', 'plaka_kodu' => '37', 'telefon_kodu' => '366'],
            ['country_id' => 1, 'il_adi' => 'Kayseri', 'plaka_kodu' => '38', 'telefon_kodu' => '352'],
            ['country_id' => 1, 'il_adi' => 'Kırklareli', 'plaka_kodu' => '39', 'telefon_kodu' => '288'],
            ['country_id' => 1, 'il_adi' => 'Kırşehir', 'plaka_kodu' => '40', 'telefon_kodu' => '386'],
            ['country_id' => 1, 'il_adi' => 'Kocaeli', 'plaka_kodu' => '41', 'telefon_kodu' => '262'],
            ['country_id' => 1, 'il_adi' => 'Konya', 'plaka_kodu' => '42', 'telefon_kodu' => '332'],
            ['country_id' => 1, 'il_adi' => 'Kütahya', 'plaka_kodu' => '43', 'telefon_kodu' => '274'],
            ['country_id' => 1, 'il_adi' => 'Malatya', 'plaka_kodu' => '44', 'telefon_kodu' => '422'],
            ['country_id' => 1, 'il_adi' => 'Manisa', 'plaka_kodu' => '45', 'telefon_kodu' => '236'],
            ['country_id' => 1, 'il_adi' => 'Kahramanmaraş', 'plaka_kodu' => '46', 'telefon_kodu' => '344'],
            ['country_id' => 1, 'il_adi' => 'Mardin', 'plaka_kodu' => '47', 'telefon_kodu' => '482'],
            ['country_id' => 1, 'il_adi' => 'Muğla', 'plaka_kodu' => '48', 'telefon_kodu' => '252'],
            ['country_id' => 1, 'il_adi' => 'Muş', 'plaka_kodu' => '49', 'telefon_kodu' => '436'],
            ['country_id' => 1, 'il_adi' => 'Nevşehir', 'plaka_kodu' => '50', 'telefon_kodu' => '384'],
            ['country_id' => 1, 'il_adi' => 'Niğde', 'plaka_kodu' => '51', 'telefon_kodu' => '388'],
            ['country_id' => 1, 'il_adi' => 'Ordu', 'plaka_kodu' => '52', 'telefon_kodu' => '452'],
            ['country_id' => 1, 'il_adi' => 'Rize', 'plaka_kodu' => '53', 'telefon_kodu' => '464'],
            ['country_id' => 1, 'il_adi' => 'Sakarya', 'plaka_kodu' => '54', 'telefon_kodu' => '264'],
            ['country_id' => 1, 'il_adi' => 'Samsun', 'plaka_kodu' => '55', 'telefon_kodu' => '362'],
            ['country_id' => 1, 'il_adi' => 'Siirt', 'plaka_kodu' => '56', 'telefon_kodu' => '484'],
            ['country_id' => 1, 'il_adi' => 'Sinop', 'plaka_kodu' => '57', 'telefon_kodu' => '368'],
            ['country_id' => 1, 'il_adi' => 'Sivas', 'plaka_kodu' => '58', 'telefon_kodu' => '346'],
            ['country_id' => 1, 'il_adi' => 'Tekirdağ', 'plaka_kodu' => '59', 'telefon_kodu' => '282'],
            ['country_id' => 1, 'il_adi' => 'Tokat', 'plaka_kodu' => '60', 'telefon_kodu' => '356'],
            ['country_id' => 1, 'il_adi' => 'Trabzon', 'plaka_kodu' => '61', 'telefon_kodu' => '462'],
            ['country_id' => 1, 'il_adi' => 'Tunceli', 'plaka_kodu' => '62', 'telefon_kodu' => '428'],
            ['country_id' => 1, 'il_adi' => 'Şanlıurfa', 'plaka_kodu' => '63', 'telefon_kodu' => '414'],
            ['country_id' => 1, 'il_adi' => 'Uşak', 'plaka_kodu' => '64', 'telefon_kodu' => '276'],
            ['country_id' => 1, 'il_adi' => 'Van', 'plaka_kodu' => '65', 'telefon_kodu' => '432'],
            ['country_id' => 1, 'il_adi' => 'Yozgat', 'plaka_kodu' => '66', 'telefon_kodu' => '354'],
            ['country_id' => 1, 'il_adi' => 'Zonguldak', 'plaka_kodu' => '67', 'telefon_kodu' => '372'],
            ['country_id' => 1, 'il_adi' => 'Aksaray', 'plaka_kodu' => '68', 'telefon_kodu' => '382'],
            ['country_id' => 1, 'il_adi' => 'Bayburt', 'plaka_kodu' => '69', 'telefon_kodu' => '458'],
            ['country_id' => 1, 'il_adi' => 'Karaman', 'plaka_kodu' => '70', 'telefon_kodu' => '338'],
            ['country_id' => 1, 'il_adi' => 'Kırıkkale', 'plaka_kodu' => '71', 'telefon_kodu' => '318'],
            ['country_id' => 1, 'il_adi' => 'Batman', 'plaka_kodu' => '72', 'telefon_kodu' => '488'],
            ['country_id' => 1, 'il_adi' => 'Şırnak', 'plaka_kodu' => '73', 'telefon_kodu' => '486'],
            ['country_id' => 1, 'il_adi' => 'Bartın', 'plaka_kodu' => '74', 'telefon_kodu' => '378'],
            ['country_id' => 1, 'il_adi' => 'Ardahan', 'plaka_kodu' => '75', 'telefon_kodu' => '478'],
            ['country_id' => 1, 'il_adi' => 'Iğdır', 'plaka_kodu' => '76', 'telefon_kodu' => '476'],
            ['country_id' => 1, 'il_adi' => 'Yalova', 'plaka_kodu' => '77', 'telefon_kodu' => '226'],
            ['country_id' => 1, 'il_adi' => 'Karabük', 'plaka_kodu' => '78', 'telefon_kodu' => '370'],
            ['country_id' => 1, 'il_adi' => 'Kilis', 'plaka_kodu' => '79', 'telefon_kodu' => '348'],
            ['country_id' => 1, 'il_adi' => 'Osmaniye', 'plaka_kodu' => '80', 'telefon_kodu' => '328'],
            ['country_id' => 1, 'il_adi' => 'Düzce', 'plaka_kodu' => '81', 'telefon_kodu' => '380'],
        ];

        foreach ($provinces as $province) {
            $province['created_at'] = now();
            $province['updated_at'] = now();
            $province['status'] = true; // Context7: status field

            DB::table('iller')->updateOrInsert(
                ['plaka_kodu' => $province['plaka_kodu']],
                $province
            );
        }

        $this->command->info('✅ '.count($provinces).' il eklendi (iller tablosuna)');
    }
}
