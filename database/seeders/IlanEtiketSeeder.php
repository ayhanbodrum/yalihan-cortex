<?php

namespace Database\Seeders;

use App\Models\Etiket;
use Illuminate\Database\Seeder;

class IlanEtiketSeeder extends Seeder
{
    public function run(): void
    {
        $etiketler = [
            [
                'name' => 'Fırsat',
                'slug' => 'firsat',
                'type' => 'promo',
                'icon' => 'fas fa-fire',
                'color' => '#fff',
                'bg_color' => '#FF4444',
                'badge_text' => 'Fırsat',
                'is_badge' => true,
                'status' => true,
                'order' => 1,
            ],
            [
                'name' => 'İndirim',
                'slug' => 'indirim',
                'type' => 'promo',
                'icon' => 'fas fa-percent',
                'color' => '#fff',
                'bg_color' => '#00AA00',
                'badge_text' => 'İndirim',
                'is_badge' => true,
                'status' => true,
                'order' => 2,
            ],
            [
                'name' => 'Özel Fiyat',
                'slug' => 'ozel-fiyat',
                'type' => 'promo',
                'icon' => 'fas fa-gift',
                'color' => '#fff',
                'bg_color' => '#FF9900',
                'badge_text' => 'Özel',
                'is_badge' => true,
                'status' => true,
                'order' => 3,
            ],
            [
                'name' => 'Denize Sıfır',
                'slug' => 'denize-sifir',
                'type' => 'location',
                'icon' => 'fas fa-water',
                'color' => '#0066CC',
                'bg_color' => '#E6F2FF',
                'is_badge' => false,
                'status' => true,
                'order' => 10,
            ],
            [
                'name' => 'Deniz Manzaralı',
                'slug' => 'deniz-manzarali',
                'type' => 'location',
                'icon' => 'fas fa-binoculars',
                'color' => '#0066CC',
                'bg_color' => '#E6F2FF',
                'is_badge' => false,
                'status' => true,
                'order' => 11,
            ],
            [
                'name' => 'Golden Visa',
                'slug' => 'golden-visa',
                'type' => 'investment',
                'icon' => 'fas fa-passport',
                'color' => '#FFD700',
                'bg_color' => '#FFF9E6',
                'is_badge' => true,
                'status' => true,
                'order' => 20,
                'target_url' => '/golden-visa',
            ],
            [
                'name' => 'Vatandaşlık',
                'slug' => 'vatandaslik',
                'type' => 'investment',
                'icon' => 'fas fa-flag',
                'color' => '#8B4513',
                'bg_color' => '#F5E6D3',
                'is_badge' => true,
                'status' => true,
                'order' => 21,
                'target_url' => '/vatandaslik',
            ],
            [
                'name' => 'Pasaport',
                'slug' => 'pasaport',
                'type' => 'investment',
                'icon' => 'fas fa-id-card',
                'color' => '#4A90E2',
                'bg_color' => '#E8F3FF',
                'is_badge' => true,
                'status' => true,
                'order' => 22,
            ],
            [
                'name' => 'Müstakil',
                'slug' => 'mustakil',
                'type' => 'feature',
                'icon' => 'fas fa-home',
                'color' => '#4A5568',
                'bg_color' => '#F7FAFC',
                'is_badge' => false,
                'status' => true,
                'order' => 30,
            ],
            [
                'name' => 'Özel Plajlı',
                'slug' => 'ozel-plajli',
                'type' => 'feature',
                'icon' => 'fas fa-umbrella-beach',
                'color' => '#059669',
                'bg_color' => '#D1FAE5',
                'is_badge' => false,
                'status' => true,
                'order' => 31,
            ],
            [
                'name' => 'Havuzlu',
                'slug' => 'havuzlu',
                'type' => 'feature',
                'icon' => 'fas fa-swimming-pool',
                'color' => '#0284C7',
                'bg_color' => '#E0F2FE',
                'is_badge' => false,
                'status' => true,
                'order' => 32,
            ],
            [
                'name' => 'Spa & Wellness',
                'slug' => 'spa-wellness',
                'type' => 'feature',
                'icon' => 'fas fa-spa',
                'color' => '#7C3AED',
                'bg_color' => '#EDE9FE',
                'is_badge' => false,
                'status' => true,
                'order' => 33,
            ],
        ];

        foreach ($etiketler as $etiket) {
            Etiket::updateOrCreate(
                ['slug' => $etiket['slug']],
                $etiket
            );
        }
    }
}
