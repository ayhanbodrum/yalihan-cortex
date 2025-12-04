<?php

namespace App\Http\Controllers;

use App\Http\Resources\IlanPublicResource;
use App\Models\Il;
use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Ulke;
use App\Services\CurrencyConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class IlanPublicController extends Controller
{
    /**
     * Frontend İlan Listesi
     */
    public function index(Request $request)
    {
        $query = Ilan::where('status', 'Aktif'); // Context7 compliant!

        // Kategori ID filtresi
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Kategori slug filtresi (ör: arsa, isyeri, konut)
        // Ana kategori üzerinden filtrele
        if ($request->filled('kategori_slug')) {
            $kategoriSlug = $request->kategori_slug;
            // Önce ana kategori ID'sini bul
            $anaKategori = \App\Models\IlanKategori::where('slug', $kategoriSlug)
                ->where('seviye', 0)
                ->first();

            if ($anaKategori) {
                // Ana kategori ID'si ile filtrele
                $query->where('ana_kategori_id', $anaKategori->id);
            } else {
                // Slug bulunamazsa, whereHas ile dene
                $query->whereHas('anaKategori', function ($q) use ($kategoriSlug) {
                    $q->where('slug', $kategoriSlug);
                });
            }
        }

        // İşlem tipi filtresi (Satılık/Kiralık) - kategori adı içinde arama
        // Not: islem_tipi kolonu yok, bu yüzden kategori adı veya başlık içinde arama yapıyoruz
        if ($request->filled('islem_tipi')) {
            $islemTipi = $request->islem_tipi;
            // Başlık veya açıklamada "satılık" veya "kiralık" araması
            if ($islemTipi === 'satis' || $islemTipi === 'satılık') {
                $query->where(function ($q) {
                    $q->where('baslik', 'like', '%satılık%')
                        ->orWhere('baslik', 'like', '%satilik%')
                        ->orWhere('aciklama', 'like', '%satılık%')
                        ->orWhere('aciklama', 'like', '%satilik%');
                });
            } elseif ($islemTipi === 'kiralama' || $islemTipi === 'kiralik') {
                $query->where(function ($q) {
                    $q->where('baslik', 'like', '%kiralık%')
                        ->orWhere('baslik', 'like', '%kiralik%')
                        ->orWhere('aciklama', 'like', '%kiralık%')
                        ->orWhere('aciklama', 'like', '%kiralik%');
                });
            }
        }

        // ✅ REFACTORED: Location filter (Filterable trait)
        if ($request->filled('il')) {
            $query->where('il_id', $request->il);
        }

        // ✅ REFACTORED: Price range filter (Filterable trait)
        $query->priceRange(
            $request->filled('min_fiyat') ? (float) $request->min_fiyat : null,
            $request->filled('max_fiyat') ? (float) $request->max_fiyat : null,
            'fiyat'
        );

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                    ->orWhere('aciklama', 'like', "%{$search}%");
            });
        }

        // ✅ EAGER LOADING: Select optimization ile birlikte
        $query->select([
            'id',
            'baslik',
            'fiyat',
            'para_birimi',
            'status',
            'kategori_id',
            'ana_kategori_id',
            'alt_kategori_id',
            'yayin_tipi_id',
            'il_id',
            'ilce_id',
            'slug',
            'created_at',
            'updated_at',
        ]);

        $query->with([
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'kategori:id,name',
            'anaKategori:id,name,slug',
            'altKategori:id,name,slug,parent_id',
            'yayinTipi:id,yayin_tipi', // Context7: Tablo kolonu yayin_tipi (name accessor var)
        ]);

        $query->with(['fotograflar']);

        // ✅ REFACTORED: Sort (Filterable trait)
        $query->sort($request->sort_by, $request->sort_order ?? 'desc', 'created_at');

        $ilanlar = $query->paginate(12);

        // API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => IlanPublicResource::collection($ilanlar->items()),
                'meta' => [
                    'current_page' => $ilanlar->currentPage(),
                    'last_page' => $ilanlar->lastPage(),
                    'per_page' => $ilanlar->perPage(),
                    'total' => $ilanlar->total(),
                ],
            ]);
        }

        // Filtreler için veriler
        $kategoriler = IlanKategori::where('parent_id', null)->get();
        $iller = Il::orderBy('il_adi')->get();

        return view('frontend.ilanlar.index', compact('ilanlar', 'kategoriler', 'iller'));
    }

    /**
     * Portfolio Index
     */
    public function portfolio(Request $request, CurrencyConversionService $currencyConversionService)
    {
        $query = Ilan::where('status', 'Aktif');

        // Kategori filtresi
        if ($request->filled('kategori')) {
            $query->where('ana_kategori_id', $request->kategori);
        }

        // İl filtresi
        if ($request->filled('il')) {
            $query->where('il_id', $request->il);
        }

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('baslik', 'like', "%{$search}%")
                    ->orWhere('aciklama', 'like', "%{$search}%");
            });
        }

        $currency = strtoupper(session('currency', $currencyConversionService->getDefault()));

        // EAGER LOADING: Select optimization ile birlikte
        $query->select([
            'id',
            'baslik',
            'aciklama',
            'fiyat',
            'para_birimi',
            'status',
            'kategori_id',
            'ana_kategori_id',
            'il_id',
            'ilce_id',
            'slug',
            'oda_sayisi',
            'banyo_sayisi',
            'brut_m2',
            'net_m2',
            'created_at',
            'updated_at',
        ]);

        $query->with([
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'anaKategori:id,name',
            'fotograflar:id,ilan_id,dosya_yolu,kapak_fotografi,sira',
            'etiketler:id,name,slug,type,icon,color,bg_color',
        ]);

        $properties = $query->orderBy('created_at', 'desc')->paginate(12);

        // Currency conversion for each property
        $properties->getCollection()->transform(function ($property) use ($currencyConversionService, $currency) {
            $property->converted_price = $currencyConversionService->convert(
                $property->fiyat,
                $property->para_birimi,
                $currency
            );

            return $property;
        });

        // Stats
        $totalValue = Ilan::where('status', 'Aktif')->whereNotNull('fiyat')->sum('fiyat');
        $stats = [
            'total_properties' => Ilan::count(),
            'active_properties' => Ilan::where('status', 'Aktif')->count(),
            'total_value' => $totalValue ? ($totalValue / 1000000) : 0,
            'locations' => Il::distinct()->count('id'),
        ];

        // Filtreler için veriler
        $kategoriler = IlanKategori::whereNull('parent_id')->orderBy('name')->get();
        $iller = Il::orderBy('il_adi')->get();

        return view('frontend.portfolio.index', compact('properties', 'stats', 'kategoriler', 'iller', 'currency'));
    }

    /**
     * Uluslararası Portföy Listesi
     */
    public function international(Request $request, CurrencyConversionService $currencyConversionService)
    {
        $query = Ilan::query()->where('status', 'Aktif');

        if (Schema::hasColumn('ilanlar', 'ulke_id')) {
            $query->whereNotNull('ulke_id');
        }

        $selectedFilters = $request->only([
            'country',
            'city',
            'citizenship',
            'min_price',
            'max_price',
            'property_type',
            'delivery',
            'min_area',
            'max_area',
        ]);

        $categoryTabs = [
            ['label' => 'Satılık', 'value' => 'sale'],
            ['label' => 'Kiralık', 'value' => 'rent'],
            ['label' => 'Yazlık', 'value' => 'seasonal'],
            ['label' => 'Vatandaşlığa Uygun', 'value' => 'citizenship'],
        ];

        $activeTab = $request->get('type', 'sale');

        // İşlem tipi filtresi (Satılık/Kiralık) - islem_tipi kolonu yok, başlık/açıklama içinde arama
        if ($activeTab === 'sale') {
            $query->where(function ($q) {
                $q->where('baslik', 'like', '%satılık%')
                    ->orWhere('baslik', 'like', '%satilik%')
                    ->orWhere('aciklama', 'like', '%satılık%')
                    ->orWhere('aciklama', 'like', '%satilik%');
            });
        } elseif ($activeTab === 'rent') {
            $query->where(function ($q) {
                $q->where('baslik', 'like', '%kiralık%')
                    ->orWhere('baslik', 'like', '%kiralik%')
                    ->orWhere('aciklama', 'like', '%kiralık%')
                    ->orWhere('aciklama', 'like', '%kiralik%');
            });
        }

        if ($activeTab === 'seasonal' && Schema::hasColumn('ilanlar', 'gunluk_fiyat')) {
            $query->whereNotNull('gunluk_fiyat');
        }

        if ($activeTab === 'citizenship' && Schema::hasColumn('ilanlar', 'citizenship_eligible')) {
            $query->where('citizenship_eligible', true);
        }

        if ($request->filled('country') && Schema::hasColumn('ilanlar', 'ulke_id')) {
            $country = $request->input('country');
            $query->whereHas('ulke', function ($subQuery) use ($country) {
                $subQuery->where('ulke_kodu', $country)->orWhere('id', $country);
            });
        }

        if ($request->filled('city') && Schema::hasColumn('ilanlar', 'il_id')) {
            $query->where('il_id', $request->input('city'));
        }

        if ($request->filled('citizenship') && Schema::hasColumn('ilanlar', 'citizenship_eligible')) {
            $citizenshipFilter = $request->input('citizenship');
            if ($citizenshipFilter === 'eligible') {
                $query->where('citizenship_eligible', true);
            } elseif ($citizenshipFilter === 'not-eligible') {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('citizenship_eligible')->orWhere('citizenship_eligible', false);
                });
            }
        }

        if ($request->filled('min_price')) {
            $query->where('fiyat', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('fiyat', '<=', $request->input('max_price'));
        }

        if ($request->filled('property_type') && Schema::hasColumn('ilanlar', 'ana_kategori_id')) {
            $query->where('ana_kategori_id', $request->input('property_type'));
        }

        if ($request->filled('min_area') && Schema::hasColumn('ilanlar', 'brut_m2')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('brut_m2', '>=', $request->input('min_area'));
                if (Schema::hasColumn('ilanlar', 'net_m2')) {
                    $subQuery->orWhere('net_m2', '>=', $request->input('min_area'));
                }
            });
        }

        if ($request->filled('max_area') && Schema::hasColumn('ilanlar', 'brut_m2')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('brut_m2', '<=', $request->input('max_area'));
                if (Schema::hasColumn('ilanlar', 'net_m2')) {
                    $subQuery->orWhere('net_m2', '<=', $request->input('max_area'));
                }
            });
        }

        $countries = Ulke::select(['id', 'ulke_adi', 'ulke_kodu'])
            ->orderBy('ulke_adi')
            ->get()
            ->map(function (Ulke $ulke) {
                return [
                    'id' => $ulke->id,
                    'name' => $ulke->ulke_adi,
                    'code' => $ulke->ulke_kodu,
                ];
            })
            ->values()
            ->all();

        $cities = Il::select(['id', 'il_adi'])
            ->orderBy('il_adi')
            ->get()
            ->map(function (Il $il) {
                return [
                    'id' => $il->id,
                    'name' => $il->il_adi,
                    'code' => $il->id,
                ];
            })
            ->values()
            ->all();

        $propertyTypes = IlanKategori::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(function (IlanKategori $kategori) {
                return [
                    'value' => $kategori->id,
                    'label' => $kategori->name,
                ];
            })
            ->values()
            ->all();

        $filters = [
            'countries' => $countries,
            'cities' => $cities,
            'types' => $propertyTypes,
        ];

        $statsQuery = clone $query;

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'citizenship' => Schema::hasColumn('ilanlar', 'citizenship_eligible')
                ? (clone $statsQuery)->where('citizenship_eligible', true)->count()
                : 0,
            'countries' => Schema::hasColumn('ilanlar', 'ulke_id')
                ? Ilan::where('status', 'Aktif')->whereNotNull('ulke_id')->distinct('ulke_id')->count('ulke_id')
                : Ilan::where('status', 'Aktif')->distinct('il_id')->count('il_id'),
            'average_price' => optional((clone $statsQuery)->avg('fiyat'), function ($avg) {
                return number_format($avg, 0, ',', '.').' ₺';
            }) ?? '—',
        ];

        $currency = strtoupper(session('currency', $currencyConversionService->getDefault()));

        $featured = (clone $query)
            ->select(['id', 'baslik', 'aciklama', 'fiyat', 'para_birimi', 'il_id', 'ilce_id', 'ana_kategori_id', 'created_at'])
            ->with([
                'il:id,il_adi',
                'ilce:id,ilce_adi',
                'anaKategori:id,name',
                'fotograflar:id,ilan_id,dosya_yolu,kapak_fotografi',
            ])
            ->orderByDesc('created_at')
            ->paginate(9)
            ->through(function (Ilan $ilan) use ($currencyConversionService, $currency) {
                $ilan->converted_price = $currencyConversionService->convert(
                    $ilan->fiyat,
                    $ilan->para_birimi,
                    $currency
                );

                return $ilan;
            });

        $citizenshipPrograms = [
            [
                'country' => 'Türkiye',
                'processing_time' => '6-8 Ay',
                'min_investment' => '$400.000',
                'highlights' => [
                    'Aile bireyleri için tam vatandaşlık',
                    'Hızlı süreç ve noter destekli belge yönetimi',
                    'Gayrimenkul yatırımıyla pasif gelir fırsatı',
                ],
            ],
            [
                'country' => 'Yunanistan',
                'processing_time' => '3-4 Ay',
                'min_investment' => '€250.000',
                'highlights' => [
                    'Schengen bölgesinde serbest dolaşım',
                    '5 yılda kalıcı oturum ve vatandaşlık başvurusu',
                    'Emlak gelirlerinde vergi avantajı',
                ],
            ],
            [
                'country' => 'Birleşik Arap Emirlikleri',
                'processing_time' => '2-3 Ay',
                'min_investment' => '$544.500',
                'highlights' => [
                    'Golden Visa ile uzun dönem oturum',
                    'Vergi avantajlı yatırım fırsatları',
                    'Yüksek kira getirisi ve dolar endeksi',
                ],
            ],
        ];

        $faqs = [
            [
                'question' => 'Vatandaşlık için minimum yatırım tutarı nedir?',
                'answer' => 'Her ülkenin farklı şartları vardır. Türkiye için $400.000, Yunanistan için €250.000 başlangıç tutarı gerekir. Danışmanlarımız detaylı bilgi sunar.',
            ],
            [
                'question' => 'AI rehberi nasıl çalışır?',
                'answer' => 'Bütçe, ülke tercihi, vatandaşlık beklentinizi alır ve global portföydeki ilanları analiz ederek öneri listesi oluşturur. Analiz sonuçları e-posta ile paylaşılır.',
            ],
            [
                'question' => 'Fiyatlar hangi para birimi ile gösteriliyor?',
                'answer' => 'Varsayılan olarak yerel para birimi kullanılır. Üst bardaki para birimi seçicisi ile USD, EUR veya TRY cinsinden güncel kurla görüntüleyebilirsiniz.',
            ],
            [
                'question' => 'Yurtdışı alımlarda hukuki süreç nasıl yönetiliyor?',
                'answer' => 'Partner hukuk bürolarımız tapu, noter, oturum başvuruları ve çeviri süreçlerini yönetir. Her yatırım için özel bir danışman atanır.',
            ],
        ];

        return view('frontend.ilanlar.international', [
            'featured' => $featured,
            'categoryTabs' => $categoryTabs,
            'activeTab' => $activeTab,
            'filters' => $filters,
            'selectedFilters' => $selectedFilters,
            'stats' => $stats,
            'citizenshipPrograms' => $citizenshipPrograms,
            'faqs' => $faqs,
            'currency' => $currency,
        ]);
    }

    /**
     * Frontend İlan Detayı
     */
    public function show($id)
    {
        $ilan = Ilan::with([
            'il:id,il_adi',
            'ilce:id,ilce_adi',
            'mahalle:id,mahalle_adi',
            'kategori:id,name',
            'parentKategori:id,name',
            'danisman:id,name,email,phone_number,whatsapp_number,title,profile_photo_path,instagram_profile,telegram_username',
            'ilanSahibi:id,ad,soyad,telefon',
            'fotograflar' => function ($q) {
                $q->select('id', 'ilan_id', 'dosya_yolu', 'sira', 'kapak_fotografi')
                    ->orderBy('sira');
            },
        ])
            ->where(function ($query) {
                // Context7: Support both enum and string status
                $query->where('status', \App\Enums\IlanStatus::YAYINDA->value)
                    ->orWhere('status', 'yayinda')
                    ->orWhere('status', 'Aktif');
            })
            ->findOrFail($id);

        // API response
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => new IlanPublicResource($ilan),
            ]);
        }

        // Benzer ilanlar (ListingNavigationService kullanıyoruz artık)
        $navigationService = app(\App\Services\ListingNavigationService::class);
        $similar = $navigationService->getSimilar($ilan, 4);

        return view('frontend.ilanlar.show', compact('ilan', 'similar'));
    }

    /**
     * Danışman İlanları
     */
    public function danismanIlanlari($id)
    {
        $ilanlar = Ilan::with(['il', 'ilce', 'kategori'])
            ->where('status', 'Aktif') // Context7 compliant!
            ->where('danisman_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.ilanlar.danisman', compact('ilanlar'));
    }

    /**
     * Kategori İlanları
     */
    public function kategoriIlanlari($kategoriId)
    {
        $kategori = IlanKategori::findOrFail($kategoriId);

        $ilanlar = Ilan::with(['il', 'ilce', 'kategori'])
            ->where('status', 'Aktif') // Context7 compliant!
            ->where('kategori_id', $kategoriId)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('frontend.ilanlar.kategori', compact('ilanlar', 'kategori'));
    }
}
