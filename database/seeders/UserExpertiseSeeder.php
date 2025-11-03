<?php

namespace Database\Seeders;

use App\Models\ExpertiseArea;
use App\Modules\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserExpertiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Önce tüm kullanıcı-uzmanlık ilişkilerini temizleyelim
        DB::table('user_expertise_area')->truncate();

        // Danışmanlar
        $users = User::where('role_id', 2)->get(); // Danışman rolü ID'si
        $expertiseAreas = ExpertiseArea::all();

        // Yunus Emre Gök için uzmanlık alanları
        $yunusEmre = User::where('email', 'y.emreyalihan@gmail.com')->first();
        if ($yunusEmre) {
            $konut = ExpertiseArea::where('name', 'Konut')->first();
            $arsa = ExpertiseArea::where('name', 'Arsa')->first();
            $isyeri = ExpertiseArea::where('name', 'İşyeri')->first();

            if ($konut) {
                $yunusEmre->expertiseAreas()->attach($konut->id, [
                    'experience_years' => 5,
                    'notes' => 'Kadıköy bölgesinde konut satışında uzmanlaşmıştır.',
                ]);
            }

            if ($arsa) {
                $yunusEmre->expertiseAreas()->attach($arsa->id, [
                    'experience_years' => 3,
                    'notes' => 'Arsa yatırımları konusunda danışmanlık vermektedir.',
                ]);
            }

            if ($isyeri) {
                $yunusEmre->expertiseAreas()->attach($isyeri->id, [
                    'experience_years' => 4,
                    'notes' => 'Ticari gayrimenkul konusunda uzmanlaşmıştır.',
                ]);
            }
        }

        // Atılay Önen için uzmanlık alanları
        $atilayOnen = User::where('email', 'atilay.onenn@gmail.com')->first();
        if ($atilayOnen) {
            $luxKonut = ExpertiseArea::where('name', 'Lüks Konut')->first();
            $rezidans = ExpertiseArea::where('name', 'Rezidans')->first();
            $turistikTesis = ExpertiseArea::where('name', 'Turistik Tesis')->first();

            if ($luxKonut) {
                $atilayOnen->expertiseAreas()->attach($luxKonut->id, [
                    'experience_years' => 7,
                    'notes' => 'Lüks konut satışında uzmanlaşmıştır.',
                ]);
            }

            if ($rezidans) {
                $atilayOnen->expertiseAreas()->attach($rezidans->id, [
                    'experience_years' => 5,
                    'notes' => 'Rezidans projelerinde deneyimlidir.',
                ]);
            }

            if ($turistikTesis) {
                $atilayOnen->expertiseAreas()->attach($turistikTesis->id, [
                    'experience_years' => 3,
                    'notes' => 'Turistik tesis yatırımları konusunda danışmanlık vermektedir.',
                ]);
            }
        }

        // Ayhan Küçük için uzmanlık alanları
        $ayhanKucuk = User::where('email', 'yalihanemlak@gmail.com')->first();
        if ($ayhanKucuk) {
            $konut = ExpertiseArea::where('name', 'Konut')->first();
            $arsa = ExpertiseArea::where('name', 'Arsa')->first();
            $rezidans = ExpertiseArea::where('name', 'Rezidans')->first();
            $denizManzarali = ExpertiseArea::where('name', 'Deniz Manzaralı')->first();

            if ($konut) {
                $ayhanKucuk->expertiseAreas()->attach($konut->id, [
                    'experience_years' => 10,
                    'notes' => 'Konut satışında 10 yıllık deneyime sahiptir.',
                ]);
            }

            if ($arsa) {
                $ayhanKucuk->expertiseAreas()->attach($arsa->id, [
                    'experience_years' => 8,
                    'notes' => 'Arsa yatırımları konusunda uzmanlaşmıştır.',
                ]);
            }

            if ($rezidans) {
                $ayhanKucuk->expertiseAreas()->attach($rezidans->id, [
                    'experience_years' => 6,
                    'notes' => 'Rezidans projelerinde deneyimlidir.',
                ]);
            }

            if ($denizManzarali) {
                $ayhanKucuk->expertiseAreas()->attach($denizManzarali->id, [
                    'experience_years' => 10,
                    'notes' => 'Deniz manzaralı gayrimenkul konusunda uzmanlaşmıştır.',
                ]);
            }
        }

        $this->command->info('Kullanıcı uzmanlık alanları başarıyla eklendi.');
    }
}
