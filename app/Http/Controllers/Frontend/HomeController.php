<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ilan;
use App\Services\CurrencyConversionService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function __invoke(CurrencyConversionService $currencyConversionService)
    {
        $currency = strtoupper(session('currency', $currencyConversionService->getDefault()));

        $featuredProperties = Ilan::query()
            ->where('status', 'Aktif')
            ->select([
                'id',
                'baslik',
                'aciklama',
                'fiyat',
                'para_birimi',
                'slug',
                'il_id',
                'ilce_id',
                'mahalle_id',
                'ana_kategori_id',
                'alt_kategori_id',
                'oda_sayisi',
                'banyo_sayisi',
                'brut_m2',
                'sanal_tur_url',
                'youtube_video_url',
                'latitude',
                'longitude',
                'gunluk_fiyat',
                'haftalik_fiyat',
                'aylik_fiyat',
                'islem_tipi',
                'created_at',
            ])
            ->with([
                'il:id,il_adi',
                'ilce:id,ilce_adi',
                'mahalle:id,mahalle_adi',
                'anaKategori:id,name,slug',
                'fotograflar:id,ilan_id,dosya_yolu,kapak_fotografi,sira,alt_text',
                'danisman:id,name,phone_number,office_phone,email',
            ])
            ->latest()
            ->limit(6)
            ->get()
            ->map(function (Ilan $ilan) use ($currencyConversionService, $currency) {
                $photos = $ilan->fotograflar
                    ->sortByDesc('kapak_fotografi')
                    ->sortBy('sira')
                    ->map(function ($photo) use ($ilan) {
                        return [
                            'url' => Storage::exists($photo->dosya_yolu)
                                ? Storage::url($photo->dosya_yolu)
                                : $this->placeholderImage(),
                            'alt' => $photo->alt_text ?: $ilan->baslik,
                        ];
                    })
                    ->values();

                $coverImage = $photos->first()['url'] ?? $this->placeholderImage();
                $locationParts = collect([
                    optional($ilan->mahalle)->mahalle_adi,
                    optional($ilan->ilce)->ilce_adi,
                    optional($ilan->il)->il_adi,
                ])->filter()->unique();

                $convertedPrice = $currencyConversionService->convert(
                    $ilan->fiyat,
                    $ilan->para_birimi,
                    $currency
                );

                $priceDisplay = $this->formatCurrency($convertedPrice, $currency);
                $pricePeriod = $this->determinePricePeriod($ilan);

                $badge = $this->determineBadge($ilan);

                return [
                    'id' => $ilan->id,
                    'title' => $ilan->baslik,
                    'location' => $locationParts->implode(', '),
                    'cover_image' => $coverImage,
                    'gallery' => $photos,
                    'price_display' => $priceDisplay,
                    'price_period' => $pricePeriod,
                    'beds' => $ilan->oda_sayisi ?: null,
                    'baths' => $ilan->banyo_sayisi ?: null,
                    'area' => $ilan->brut_m2 ?: null,
                    'badge' => $badge['value'],
                    'badge_text' => $badge['label'],
                    'virtual_tour_url' => $ilan->sanal_tur_url ?? $ilan->youtube_video_url,
                    'map_location' => [
                        'lat' => $ilan->latitude ?? $ilan->lat ?? null,
                        'lng' => $ilan->longitude ?? $ilan->lng ?? null,
                        'title' => $ilan->baslik,
                        'content' => $locationParts->implode(', '),
                    ],
                    'detail_payload' => [
                        'title' => $ilan->baslik,
                        'location' => $locationParts->implode(', '),
                        'price' => $priceDisplay,
                        'description' => Str::limit(strip_tags((string) $ilan->aciklama), 280),
                        'features' => $this->propertyFeatures($ilan),
                        'link' => route('yalihan.property.detail', $ilan->id),
                    ],
                    'contact_payload' => [
                        'id' => $ilan->id,
                        'title' => $ilan->baslik,
                        'location' => $locationParts->implode(', '),
                        'contact_url' => url('/iletisim'),
                        'danisman' => $ilan->danisman ? [
                            'name' => $ilan->danisman->name,
                            'phone' => $ilan->danisman->phone_number,
                            'office_phone' => $ilan->danisman->office_phone,
                            'email' => $ilan->danisman->email,
                        ] : null,
                    ],
                    'share_url' => route('yalihan.property.detail', $ilan->id),
                ];
            });

        $stats = [
            'active_listings' => Ilan::where('status', 'Aktif')->count(),
            'experience_years' => 20,
            'happy_customers' => Ilan::count() * 2 + 500, // simple heuristic
        ];

        return view('yaliihan-home-clean', [
            'featuredProperties' => $featuredProperties,
            'stats' => $stats,
        ]);
    }

    private function placeholderImage(): string
    {
        return 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1200&q=80';
    }

    private function formatCurrency(float $amount, string $currency): string
    {
        $formatted = number_format($amount, 0, ',', '.');

        return match ($currency) {
            'TRY' => "₺{$formatted}",
            'USD' => "  {$formatted}",
            'EUR' => "€{$formatted}",
            default => $formatted . ' ' . $currency,
        };
    }

    private function determinePricePeriod(Ilan $ilan): ?string
    {
        return match (true) {
            !is_null($ilan->gunluk_fiyat) => '/gün',
            !is_null($ilan->haftalik_fiyat) => '/hafta',
            !is_null($ilan->aylik_fiyat) => '/ay',
            default => null,
        };
    }

    private function determineBadge(Ilan $ilan): array
    {
        $badge = ['value' => 'sale', 'label' => 'Satılık'];

        if (Str::contains(Str::lower($ilan->baslik . ' ' . $ilan->aciklama), ['kiralık', 'kiralik'])) {
            $badge = ['value' => 'rent', 'label' => 'Kiralık'];
        }

        if ($ilan->anaKategori && Str::contains(Str::lower($ilan->anaKategori->name), ['yazlık', 'villa'])) {
            $badge = ['value' => 'featured', 'label' => 'Öne Çıkan'];
        }

        return $badge;
    }

    private function propertyFeatures(Ilan $ilan): array
    {
        return collect([
            ['label' => 'Oda', 'value' => $ilan->oda_sayisi],
            ['label' => 'Banyo', 'value' => $ilan->banyo_sayisi],
            ['label' => 'Brüt m²', 'value' => $ilan->brut_m2],
            ['label' => 'Kategori', 'value' => optional($ilan->anaKategori)->name],
        ])->filter(fn ($feature) => filled($feature['value']))->values()->all();
    }
}
