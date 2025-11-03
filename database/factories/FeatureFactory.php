<?php

namespace Database\Factories;

use App\Models\Feature;
use App\Models\FeatureCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'category_id' => FeatureCategory::factory(),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'type' => 'text',
            'enabled' => true,
            'display_order' => 0,
        ];
    }
}
