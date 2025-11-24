<?php

namespace Database\Factories;

use App\Enums\IlanStatus;
use App\Models\Ilan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class IlanFactory extends Factory
{
    protected $model = Ilan::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'baslik' => $title,
            'slug' => Str::slug($title.' '.$this->faker->unique()->uuid()),
            'aciklama' => $this->faker->paragraph(),
            'fiyat' => $this->faker->numberBetween(100000, 2000000),
            'para_birimi' => 'TRY',
            'status' => IlanStatus::YAYINDA->value,
        ];
    }
}