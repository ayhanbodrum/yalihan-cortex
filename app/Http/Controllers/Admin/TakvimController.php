<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Season;
use App\Models\Ilan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TakvimController extends AdminController
{
    /**
     * Display a listing of the resource.
     * Context7: Takvim yönetimi ana sayfası
     */
    public function index()
    {
        try {
            // Mevcut ay ve yıl bilgilerini al
            $currentMonth = now()->month;
            $currentYear = now()->year;

            // Takvim etkinliklerini al (örnek veri)
            $events = $this->getCalendarEvents($currentMonth, $currentYear);

            // İstatistikler
            $stats = [
                'total_events' => collect($events)->count(),
                'this_week' => collect($events)->filter(function($event) {
                    return Carbon::parse($event['date'])->isCurrentWeek();
                })->count(),
                'upcoming' => collect($events)->filter(function($event) {
                    return Carbon::parse($event['date'])->isFuture();
                })->count()
            ];

            // PHASE 2: Yazlık-kiralama altında olduğu için admin.yazlik-kiralama.takvim view'ı kullan
            return view('admin.yazlik-kiralama.takvim', compact('events', 'stats', 'currentMonth', 'currentYear'));
        } catch (\Exception $e) {
            return back()->with('error', 'Takvim yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     * Context7: Yeni etkinlik oluşturma formu
     */
    public function create()
    {
        try {
            $eventTypes = [
                'meeting' => 'Toplantı',
                'viewing' => 'İlan Görüntüleme',
                'call' => 'Müşteri Araması',
                'followup' => 'Takip',
                'other' => 'Diğer'
            ];

            return view('admin.takvim.create', compact('eventTypes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     * Context7: Yeni etkinlik kaydetme
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date',
                'event_time' => 'required',
                'type' => 'required|in:meeting,viewing,call,followup,other',
                'location' => 'nullable|string|max:255',
                'attendees' => 'nullable|string'
            ]);

            // Context7: Veri tabanına kaydetme işlemi
            // Gerçek implementasyonda burada events tablosuna kayıt yapılacak
            // ✅ TODO TAMAMLANDI: Event model ile kaydet
            $event = Event::create([
                'ilan_id' => $request->ilan_id,
                'check_in' => $request->event_date ?? $request->start ?? now(),
                'check_out' => $request->end ?? now()->addDay(),
                'check_in_time' => $request->event_time ?? '14:00',
                'check_out_time' => '11:00',
                'guest_name' => $request->title ?? 'Guest',
                'guest_email' => $request->guest_email ?? '',
                'guest_phone' => $request->guest_phone ?? '',
                'guest_count' => $request->attendees ?? 1,
                'daily_price' => $request->daily_price ?? 0,
                'total_price' => $request->total_price ?? 0,
                'status' => $request->status ?? 'pending',
                'payment_status' => 'unpaid',
                'special_requests' => $request->description,
                'notes' => $request->location,
                'source' => 'admin',
            ]);

            $eventData = $event->toArray();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Etkinlik başarıyla oluşturuldu',
                    'data' => $eventData
                ], 201);
            }

            return redirect()->route('admin.takvim.index')->with('success', 'Etkinlik başarıyla oluşturuldu');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Etkinlik oluşturulurken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Etkinlik oluşturulurken hata: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Context7: Belirli etkinlik detayları
     */
    public function show($id)
    {
        try {
            // Örnek etkinlik verisi
            $event = $this->getSampleEvent($id);

            if (!$event) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Etkinlik bulunamadı'
                    ], 404);
                }

                return redirect()->route('admin.takvim.index')->with('error', 'Etkinlik bulunamadı');
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $event
                ]);
            }

            return view('admin.takvim.show', compact('event'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Etkinlik detayları alınırken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Etkinlik detayları alınırken hata: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Context7: Etkinlik düzenleme formu
     */
    public function edit($id)
    {
        try {
            $event = $this->getSampleEvent($id);

            if (!$event) {
                return redirect()->route('admin.takvim.index')->with('error', 'Etkinlik bulunamadı');
            }

            $eventTypes = [
                'meeting' => 'Toplantı',
                'viewing' => 'İlan Görüntüleme',
                'call' => 'Müşteri Araması',
                'followup' => 'Takip',
                'other' => 'Diğer'
            ];

            return view('admin.takvim.edit', compact('event', 'eventTypes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Form yüklenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     * Context7: Etkinlik güncelleme
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date',
                'event_time' => 'required',
                'type' => 'required|in:meeting,viewing,call,followup,other',
                'location' => 'nullable|string|max:255',
                'attendees' => 'nullable|string'
            ]);

            // Context7: Güncelleme işlemi
            $eventData = [
                'title' => $request->title,
                'description' => $request->description,
                'event_date' => $request->event_date,
                'event_time' => $request->event_time,
                'type' => $request->type,
                'location' => $request->location,
                'attendees' => $request->attendees,
                'updated_at' => now()
            ];

            // ✅ TODO TAMAMLANDI: Event model ile güncelleme
            $event = Event::findOrFail($id);
            $event->update([
                'check_in' => $request->event_date ?? $request->start ?? $event->check_in,
                'check_out' => $request->end ?? $event->check_out,
                'guest_name' => $request->title ?? $event->guest_name,
                'guest_count' => $request->attendees ?? $event->guest_count,
                'status' => $request->status ?? $event->status,
                'special_requests' => $request->description,
                'notes' => $request->location,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Etkinlik başarıyla güncellendi',
                    'data' => $eventData
                ]);
            }

            return redirect()->route('admin.takvim.index')->with('success', 'Etkinlik başarıyla güncellendi');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Etkinlik güncellenirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Etkinlik güncellenirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     * Context7: Etkinlik silme
     */
    public function destroy($id)
    {
        try {
            // ✅ TODO TAMAMLANDI: Event model ile silme
            $event = Event::findOrFail($id);
            $event->delete(); // Soft delete

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Etkinlik başarıyla silindi'
                ]);
            }

            return redirect()->route('admin.takvim.index')->with('success', 'Etkinlik başarıyla silindi');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Etkinlik silinirken hata: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Etkinlik silinirken hata: ' . $e->getMessage());
        }
    }

    /**
     * Context7: Ay bazında etkinlik listesi
     */
    public function getMonthEvents(Request $request)
    {
        try {
            $month = $request->get('month', now()->month);
            $year = $request->get('year', now()->year);

            $events = $this->getCalendarEvents($month, $year);

            return response()->json([
                'success' => true,
                'events' => $events,
                'month' => $month,
                'year' => $year
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Etkinlikler alınırken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Etkinlik arama
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $type = $request->get('type', '');

            // Mock arama sonuçları
            $events = collect($this->getCalendarEvents())->filter(function($event) use ($query, $type) {
                $matchesQuery = empty($query) ||
                    str_contains(strtolower($event['title']), strtolower($query)) ||
                    str_contains(strtolower($event['description'] ?? ''), strtolower($query));

                $matchesType = empty($type) || $event['type'] === $type;

                return $matchesQuery && $matchesType;
            })->values();

            return response()->json([
                'success' => true,
                'events' => $events,
                'query' => $query,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Arama yapılırken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Context7: Örnek takvim etkinlikleri
     */
    private function getCalendarEvents($month = null, $year = null)
    {
        // Mock data - gerçek implementasyonda veritabanından gelecek
        return [
            [
                'id' => 1,
                'title' => 'Müşteri Toplantısı',
                'description' => 'Bay Ahmet ile villa görüşmesi',
                'date' => now()->addDays(2)->format('Y-m-d'),
                'time' => '10:00',
                'type' => 'meeting',
                'location' => 'Ofis',
                'attendees' => 'Bay Ahmet, Danışman Ali'
            ],
            [
                'id' => 2,
                'title' => 'İlan Fotoğraf Çekimi',
                'description' => 'Yeni villa için fotoğraf çekimi',
                'date' => now()->addDays(5)->format('Y-m-d'),
                'time' => '14:00',
                'type' => 'viewing',
                'location' => 'Bodrum, Yalıkavak',
                'attendees' => 'Fotoğrafçı Mehmet'
            ],
            [
                'id' => 3,
                'title' => 'Takip Araması',
                'description' => 'Hanım Zeynep takip araması',
                'date' => now()->addDays(1)->format('Y-m-d'),
                'time' => '09:30',
                'type' => 'followup',
                'location' => 'Telefon',
                'attendees' => 'Hanım Zeynep'
            ]
        ];
    }

    /**
     * Context7: Örnek etkinlik detayı
     */
    private function getSampleEvent($id)
    {
        $events = $this->getCalendarEvents();
        return collect($events)->firstWhere('id', (int)$id);
    }

    /**
     * Sezonlar sayfası
     * Context7: Yazlık kiralama sezonları yönetimi
     */
    public function sezonlar()
    {
        try {
            // Sezon verilerini collection olarak al (blade'de ->count() kullanımı için)
            $sezonlar = collect([
                [
                    'id' => 1,
                    'adi' => 'Yaz Sezonu',
                    'baslangic' => '2024-06-01',
                    'bitis' => '2024-09-15',
                    'gunluk_fiyat' => 5000,
                    'enabled' => true,
                    'sezon_tipi' => 'yuksek',
                    'minimum_konaklama_gun' => 7,
                    'maksimum_konaklama_gun' => 30
                ],
                [
                    'id' => 2,
                    'adi' => 'Ara Sezon',
                    'baslangic' => '2024-09-16',
                    'bitis' => '2024-11-30',
                    'gunluk_fiyat' => 3000,
                    'enabled' => true,
                    'sezon_tipi' => 'orta',
                    'minimum_konaklama_gun' => 3,
                    'maksimum_konaklama_gun' => 15
                ],
                [
                    'id' => 3,
                    'adi' => 'Kış Sezonu',
                    'baslangic' => '2024-12-01',
                    'bitis' => '2025-05-31',
                    'gunluk_fiyat' => 2000,
                    'enabled' => false,
                    'sezon_tipi' => 'dusuk',
                    'minimum_konaklama_gun' => 2,
                    'maksimum_konaklama_gun' => 10
                ]
            ]);

            return view('admin.takvim.sezonlar', compact('sezonlar'));
        } catch (\Exception $e) {
            return back()->with('error', 'Sezonlar yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sezon kaydetme
     * Context7: Yeni sezon oluşturma
     */
    public function storeSezon(Request $request)
    {
        try {
            $request->validate([
                'adi' => 'required|string|max:255',
                'baslangic' => 'required|date',
                'bitis' => 'required|date|after:baslangic',
                'gunluk_fiyat' => 'required|numeric|min:0'
            ]);

            // ✅ TODO TAMAMLANDI: Season model ile kaydet
            $season = Season::create([
                'ilan_id' => $request->ilan_id,
                'name' => $request->adi,
                'type' => $request->sezon_tipi ?? 'ozel',
                'start_date' => $request->baslangic,
                'end_date' => $request->bitis,
                'daily_price' => $request->gunluk_fiyat,
                'weekly_price' => $request->haftalik_fiyat,
                'monthly_price' => $request->aylik_fiyat,
                'minimum_stay' => $request->minimum_konaklama ?? 1,
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sezon başarıyla oluşturuldu!',
                'data' => $season
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Sezon güncelleme
     * Context7: Mevcut sezon güncelleme
     */
    public function updateSezon(Request $request, $id)
    {
        try {
            $request->validate([
                'adi' => 'required|string|max:255',
                'baslangic' => 'required|date',
                'bitis' => 'required|date|after:baslangic',
                'gunluk_fiyat' => 'required|numeric|min:0'
            ]);

            // ✅ TODO TAMAMLANDI: Season model ile güncelleme
            $season = Season::findOrFail($id);
            $season->update([
                'name' => $request->adi,
                'start_date' => $request->baslangic,
                'end_date' => $request->bitis,
                'daily_price' => $request->gunluk_fiyat,
                'weekly_price' => $request->haftalik_fiyat,
                'monthly_price' => $request->aylik_fiyat,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sezon başarıyla güncellendi!',
                'data' => $season
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Sezon silme
     * Context7: Sezon silme işlemi
     */
    public function destroySezon($id)
    {
        try {
            // ✅ TODO TAMAMLANDI: Season model ile silme
            $season = Season::findOrFail($id);
            $season->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Sezon başarıyla silindi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hata: ' . $e->getMessage()
            ], 400);
        }
    }
}
