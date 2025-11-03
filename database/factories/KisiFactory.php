<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kisi>
 */
class KisiFactory extends Factory
{
    protected $model = \App\Models\Kisi::class;

    public function definition(): array
    {
        return [
            'ad' => $this->faker->firstName(),
            'soyad' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefon' => $this->faker->phoneNumber(),
            'adres' => $this->faker->address(),
            'tc_kimlik' => $this->faker->numerify('###########'),
            'dogum_tarihi' => $this->faker->date(),
            'cinsiyet' => $this->faker->randomElement(['erkek', 'kadın']),
            'meslek' => $this->faker->jobTitle(),
            'notlar' => $this->faker->optional()->paragraph(),
        ];
    }
}
