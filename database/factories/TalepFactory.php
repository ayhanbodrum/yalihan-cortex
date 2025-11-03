<?php

namespace Database\Factories;

use App\Models\Kisi;
use App\Models\Talep;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TalepFactory extends Factory
{
    protected $model = Talep::class;

    public function definition(): array
    {
        return [
            'baslik' => $this->faker->sentence(3),
            'kisi_id' => Kisi::factory(),
            'danisman_id' => User::factory(),
            'talep_turu' => $this->faker->randomElement(['satis', 'kira']),
            'emlak_turu' => $this->faker->randomElement(['daire', 'villa', 'arsa', 'isyeri']),
            'butce_min' => $this->faker->numberBetween(100000, 500000),
            'butce_max' => $this->faker->numberBetween(500000, 2000000),
            'para_birimi' => 'TL',
            'status' => $this->faker->randomElement(['active', 'pasif', 'tamamlandi']),
            'oncelik' => $this->faker->randomElement(['düşük', 'normal', 'yüksek', 'acil']),
            'il' => $this->faker->city(),
            'ilce' => $this->faker->citySuffix(),
            'mahalle' => $this->faker->streetName(),
            'aciklama' => $this->faker->paragraph(),
            'ilan_turu' => $this->faker->randomElement(['konut', 'isyeri', 'arsa']),
            'yayinlama_tipi' => $this->faker->randomElement(['satilik', 'kiralik']),
            'il_id' => null, // Gerekirse Il modeli oluşturulup atanabilir
            'ilce_id' => null, // Gerekirse Ilce modeli oluşturulup atanabilir
            'min_metrekare' => $this->faker->numberBetween(50, 150),
            'max_metrekare' => $this->faker->numberBetween(150, 500),
            'min_fiyat' => $this->faker->numberBetween(50000, 200000),
            'max_fiyat' => $this->faker->numberBetween(200000, 1000000),
            'oda_sayisi' => $this->faker->randomElement(['1+1', '2+1', '3+1', '4+1', '5+1']),
            'bina_yasi' => $this->faker->numberBetween(0, 30),
        ];
    }
}
