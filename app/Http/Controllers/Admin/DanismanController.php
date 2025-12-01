<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class DanismanController extends AdminController
{
    public function index(Request $request)
    {
        // ✅ EAGER LOADING: Roles relationship
        $query = User::with('roles:id,name');

        // Context7: Spatie Permission uses 'roles' (plural), not 'role'
        $query->whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        });

        // ✅ Context7: Varsayılan olarak sadece aktif danışmanları göster (status = 1)
        if (! $request->has('status')) {
            $query->where('status', 1);
        }

        $search = trim((string) $request->get('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                // Context7: phone_number kolonu migration sonrası eklenecek, şimdilik kontrol edelim
                if (Schema::hasColumn('users', 'phone_number')) {
                    $q->orWhere('phone_number', 'like', "%{$search}%");
                }
            });
        }
        $status = $request->get('status');
        if ($status === '1' || $status === 1) {
            $query->where('status', 1);
        } elseif ($status === '0' || $status === 0) {
            $query->where('status', 0);
        }
        $online = $request->get('online');
        if ($online === 'Online') {
            $query->whereNotNull('last_activity_at')->where('last_activity_at', '>', now()->subMinutes(5));
        } elseif ($online === 'Offline') {
            $query->where(function ($q) {
                $q->whereNull('last_activity_at')->orWhere('last_activity_at', '<=', now()->subMinutes(5));
            });
        }
        $sort = $request->get('sort');
        if ($sort === 'name_asc') {
            $query->orderBy('name');
        } elseif ($sort === 'name_desc') {
            $query->orderByDesc('name');
        } elseif ($sort === 'created_asc') {
            $query->orderBy('created_at');
        } elseif ($sort === 'created_desc') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderByDesc('created_at');
        }
        $danismanlar = $query->paginate(20);
        $danismanlar->appends($request->query());
        $istatistikler = [
            'toplam_danisman' => User::whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })->count(),
            'status_danisman' => User::whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })->where('status', 1)->count(),
            'online_danisman' => User::whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })->whereNotNull('last_activity_at')->where('last_activity_at', '>', now()->subMinutes(5))->count(),
            'ortalama_performans' => 0,
        ];
        $filters = [
            'search' => $search,
            'status' => $status,
            'online' => $online,
            'sort' => $sort,
        ];

        // ✅ Context7: View için gerekli değişkenler
        $statuslar = ['1' => 'Aktif', '0' => 'Pasif']; // Filter için

        return $this->renderIfExists('admin.danisman.index', compact('danismanlar', 'istatistikler', 'filters', 'statuslar'));
    }

    public function create()
    {
        return $this->renderIfExists('admin.danisman.create', []);
    }

    public function show(Request $request, User $danisman)
    {
        // Context7: Show danışman details with tabs (Remax style)
        // Route model binding ile gelen $danisman otomatik olarak User modeli

        // Danışman rolünü kontrol et
        if (! $danisman->hasRole('danisman')) {
            abort(404, 'Bu kullanıcı bir danışman değil');
        }

        // Eager loading
        $danisman->load([
            'roles:id,name',
            'ilanlar' => function ($q) {
                $q->where('status', 'Aktif')->latest()->limit(10);
            },
        ]);

        // ✅ Context7: Danışman yorumları (tablo kontrolü ile)
        if (Schema::hasTable('danisman_yorumlari')) {
            $danisman->load([
                'onayliDanismanYorumlari' => function ($q) {
                    $q->with('kisi:id,tam_ad,email')->orderBy('created_at', 'desc');
                },
            ]);
        }

        // Tab seçimi (hakkimda, portfoy, yorumlar)
        $activeTab = $request->get('t', 'hakkimda'); // Default: hakkımda

        // İstatistikler
        $danismanId = $danisman->id;
        $toplamIlan = \App\Models\Ilan::where('danisman_id', $danismanId)->count();
        $aktifIlan = \App\Models\Ilan::where('danisman_id', $danismanId)->where('status', 'Aktif')->count();
        $toplamMusteri = \App\Models\Kisi::where('danisman_id', $danismanId)->count();
        $aktifMusteri = \App\Models\Kisi::where('danisman_id', $danismanId)->where('status', 'Aktif')->count();
        $basariOrani = $toplamIlan > 0 ? round(($aktifIlan / $toplamIlan) * 100, 1) : 0.0;

        // ✅ Talep sayısı
        $toplamTalep = \App\Models\Talep::where('danisman_id', $danismanId)->count();
        $aktifTalep = \App\Models\Talep::where('danisman_id', $danismanId)
            ->where('status', 'Aktif')
            ->count();

        // Yorum istatistikleri (✅ Context7: Tablo kontrolü ile)
        $toplamYorum = 0;
        $onayliYorum = 0;
        $ortalamaRating = 0;

        if (Schema::hasTable('danisman_yorumlari')) {
            $toplamYorum = $danisman->danismanYorumlari()->count();
            $onayliYorum = $danisman->onayliDanismanYorumlari()->count();
            $ortalamaRating = $danisman->onayliDanismanYorumlari()->avg('rating') ?? 0;
        }

        // Performans verileri
        $performans = [
            'toplam_ilan' => $toplamIlan,
            'status_ilan' => $aktifIlan,
            'toplam_talep' => $toplamTalep,
            'aktif_talep' => $aktifTalep,
            'basari_orani' => $basariOrani,
            'musteri_memnuniyeti' => 80.0,
            'ai_skor' => 70.0,
            'performans_puani' => 85,
            'ai_degerlendirme' => 'Normal',
            'toplam_yorum' => $toplamYorum,
            'onayli_yorum' => $onayliYorum,
            'ortalama_rating' => round($ortalamaRating, 1),
        ];

        // Portföy (aktif ilanlar)
        $portfoy = $danisman->ilanlar()
            ->where('status', 'Aktif')
            ->latest()
            ->paginate(12, ['*'], 'portfoy_page');

        // Yorumlar (onaylı) (✅ Context7: Tablo kontrolü ile)
        $yorumlar = null;
        if (Schema::hasTable('danisman_yorumlari')) {
            $yorumlar = $danisman->onayliDanismanYorumlari()
                ->with('kisi:id,tam_ad,email')
                ->latest()
                ->paginate(10, ['*'], 'yorum_page');
        } else {
            // ✅ Context7: Tablo yoksa boş paginator oluştur
            $yorumlar = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                10,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        $aiOneriler = [];

        return view('admin.danisman.show', compact(
            'danisman',
            'performans',
            'aiOneriler',
            'activeTab',
            'portfoy',
            'yorumlar'
        ));
    }

    public function edit(User $danisman)
    {
        // Route model binding ile gelen $danisman otomatik olarak User modeli

        // Danışman rolünü kontrol et
        if (! $danisman->hasRole('danisman')) {
            abort(404, 'Bu kullanıcı bir danışman değil');
        }

        return view('admin.danisman.edit', compact('danisman'));
    }

    public function store(Request $request)
    {
        // Context7: phone_number kullan (telefon değil)
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'telefon' => 'nullable|string|max:20', // Backward compatibility
            'uzmanlik_alanlari' => 'nullable|array',
            'uzmanlik_alanlari.*' => 'string|max:100',
            'deneyim_yili' => 'nullable|integer|min:0|max:50',
            'title' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100|in:danisman,asistan,broker', // ✅ Sadece bu 3 değer
            // 'department' => 'nullable|string|max:100', // ❌ KALDIRILDI
            'office_address' => 'nullable|string|max:500',
            'password' => 'required|string|min:8|confirmed',
            'lisans_no' => 'nullable|string|max:50',
            'status' => 'required|string|in:taslak,onay_bekliyor,aktif,satildi,kiralandi,pasif,arsivlendi,1,0',
            'status_text' => 'nullable|string|max:50',
        ]);

        try {
            // Context7: Tüm danışman bilgileri users tablosunda
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'status' => $request->status ? 1 : 0,
                'title' => 'Danışman', // Default title
            ];

            // Context7: phone_number kullan
            if ($request->filled('phone_number')) {
                $userData['phone_number'] = $request->phone_number;
            } elseif ($request->filled('telefon')) {
                // Backward compatibility
                $userData['phone_number'] = $request->telefon;
            }

            // Context7: office_address kullan (adres değil)
            if ($request->filled('office_address')) {
                $userData['office_address'] = $request->office_address;
            } elseif ($request->filled('adres')) {
                // Backward compatibility
                $userData['office_address'] = $request->adres;
            }

            // Title
            if ($request->filled('title')) {
                $userData['title'] = $request->title;
            }

            // Position: Sadece izin verilen değerler
            if ($request->filled('position')) {
                $allowedPositions = ['danisman', 'asistan', 'broker'];
                if (in_array($request->position, $allowedPositions)) {
                    $userData['position'] = $request->position;
                }
            }

            // Department: KALDIRILDI - Artık kullanılmıyor
            // if ($request->filled('department')) {
            //     $userData['department'] = $request->department;
            // }

            if ($request->filled('lisans_no')) {
                $userData['lisans_no'] = $request->lisans_no;
            }

            // Context7: uzmanlik_alanlari JSON array olarak kaydet
            if ($request->has('uzmanlik_alanlari') && is_array($request->uzmanlik_alanlari)) {
                // Sadece izin verilen değerleri filtrele
                $allowedAreas = ['Konut', 'Arsa', 'İşyeri', 'Yazlık', 'Turistik Tesis'];
                $filteredAreas = array_filter($request->uzmanlik_alanlari, function ($area) use ($allowedAreas) {
                    return in_array($area, $allowedAreas);
                });
                if (! empty($filteredAreas)) {
                    $userData['uzmanlik_alanlari'] = array_values($filteredAreas); // JSON array olarak kaydet
                }
            }

            // Eski tek seçim için backward compatibility
            if ($request->filled('uzmanlik_alani') && ! $request->has('uzmanlik_alanlari')) {
                $userData['uzmanlik_alani'] = $request->uzmanlik_alani;
            }

            // Deneyim yılı
            if ($request->filled('deneyim_yili')) {
                $userData['deneyim_yili'] = (int) $request->deneyim_yili;
            }

            // Status: String değerleri boolean'a çevir ve status_text olarak kaydet
            if ($request->filled('status')) {
                $statusValue = $request->status;

                // String durumları kontrol et
                if (in_array($statusValue, ['taslak', 'onay_bekliyor', 'aktif', 'satildi', 'kiralandi', 'pasif', 'arsivlendi'])) {
                    // String durum: status_text'e kaydet
                    $userData['status_text'] = $statusValue;
                    // Boolean status'u da güncelle (backward compatibility)
                    $userData['status'] = in_array($statusValue, ['taslak', 'onay_bekliyor', 'pasif']) ? 0 : 1;
                } elseif (in_array($statusValue, ['aktif', '1', 1, true])) {
                    // Boolean aktif
                    $userData['status'] = 1;
                    $userData['status_text'] = 'aktif';
                } elseif (in_array($statusValue, ['pasif', '0', 0, false])) {
                    // Boolean pasif
                    $userData['status'] = 0;
                    $userData['status_text'] = 'pasif';
                } else {
                    // Default aktif
                    $userData['status'] = 1;
                    $userData['status_text'] = 'aktif';
                }
            } else {
                // Default aktif
                $userData['status'] = 1;
                $userData['status_text'] = 'aktif';
            }

            $user = User::create($userData);

            // Assign danisman role
            $user->assignRole('danisman');

            return redirect()
                ->route('admin.danisman.index')
                ->with('success', 'Danışman başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Danışman oluşturma hatası: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Danışman oluşturulurken hata oluştu: '.$e->getMessage());
        }
    }

    public function update(Request $request, User $danisman)
    {
        // Route model binding ile gelen $danisman otomatik olarak User modeli

        // Danışman rolünü kontrol et
        if (! $danisman->hasRole('danisman')) {
            abort(404, 'Bu kullanıcı bir danışman değil');
        }

        // Context7: Danışman güncelleme
        $request->validate([
            'name' => 'nullable|string|max:255', // Backward compatibility - ad ve soyad birleştiriliyor
            'ad' => 'required_without:name|string|max:100',
            'soyad' => 'required_without:name|string|max:100',
            'email' => 'required|email|unique:users,email,'.$danisman->id,
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'title' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100|in:danisman,asistan,broker', // ✅ Sadece bu 3 değer
            // 'department' => 'nullable|string|max:100', // ❌ KALDIRILDI
            'bio' => 'nullable|string',
            'lisans_no' => 'nullable|string|max:50',
            'uzmanlik_alanlari' => 'nullable|array',
            'uzmanlik_alanlari.*' => 'string|max:100',
            'deneyim_yili' => 'nullable|integer|min:0|max:50',
            'office_address' => 'nullable|string|max:500',
            'office_phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'expertise_summary' => 'nullable|string',
            'certificates_info' => 'nullable|string',
            'instagram_profile' => 'nullable|url|max:255',
            'linkedin_profile' => 'nullable|url|max:255',
            'facebook_profile' => 'nullable|url|max:255',
            'twitter_profile' => 'nullable|url|max:255',
            'youtube_channel' => 'nullable|url|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'nullable|string|in:taslak,onay_bekliyor,aktif,satildi,kiralandi,pasif,arsivlendi,1,0',
            'status_text' => 'nullable|string|max:50',
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        try {
            // Context7: Ad ve Soyad'ı birleştir (eğer ayrı gönderildiyse)
            $fullName = $request->name;
            if ($request->filled('ad') || $request->filled('soyad')) {
                $ad = trim($request->ad ?? '');
                $soyad = trim($request->soyad ?? '');
                $fullName = trim($ad.' '.$soyad);
            } elseif (empty($fullName) && $danisman->name) {
                // Mevcut değeri koru
                $fullName = $danisman->name;
            }

            $userData = [
                'name' => $fullName,
                'email' => $request->email,
                'title' => $request->title,
                'bio' => $request->bio,
                'phone_number' => $request->phone_number,
                'lisans_no' => $request->lisans_no,
                'deneyim_yili' => (int) ($request->deneyim_yili ?? 0),
                'office_address' => $request->office_address,
                'office_phone' => $request->office_phone,
                'whatsapp_number' => $request->whatsapp_number,
                'expertise_summary' => $request->expertise_summary,
                'certificates_info' => $request->certificates_info,
                'instagram_profile' => $request->instagram_profile,
                'linkedin_profile' => $request->linkedin_profile,
                'facebook_profile' => $request->facebook_profile,
                'twitter_profile' => $request->twitter_profile,
                'youtube_channel' => $request->youtube_channel,
                'website' => $request->website,
            ];

            // Position: Sadece izin verilen değerler
            if ($request->filled('position')) {
                $allowedPositions = ['danisman', 'asistan', 'broker'];
                if (in_array($request->position, $allowedPositions)) {
                    $userData['position'] = $request->position;
                } else {
                    // Geçersiz değer gönderilirse mevcut değeri koru veya null yap
                    $userData['position'] = null;
                }
            }

            // Status: String değerleri boolean'a çevir ve status_text olarak kaydet
            if ($request->filled('status')) {
                $statusValue = $request->status;

                // String durumları kontrol et
                if (in_array($statusValue, ['taslak', 'onay_bekliyor', 'aktif', 'satildi', 'kiralandi', 'pasif', 'arsivlendi'])) {
                    // String durum: status_text'e kaydet
                    $userData['status_text'] = $statusValue;
                    // Boolean status'u da güncelle (backward compatibility)
                    $userData['status'] = in_array($statusValue, ['taslak', 'onay_bekliyor', 'pasif']) ? 0 : 1;
                } elseif (in_array($statusValue, ['aktif', '1', 1, true])) {
                    // Boolean aktif
                    $userData['status'] = 1;
                    $userData['status_text'] = 'aktif';
                } elseif (in_array($statusValue, ['pasif', '0', 0, false])) {
                    // Boolean pasif
                    $userData['status'] = 0;
                    $userData['status_text'] = 'pasif';
                } else {
                    // Mevcut değeri koru
                    $userData['status'] = $danisman->status;
                    $userData['status_text'] = $danisman->status_text ?? ($danisman->status ? 'aktif' : 'pasif');
                }
            } else {
                // Mevcut değeri koru
                $userData['status'] = $danisman->status;
                $userData['status_text'] = $danisman->status_text ?? ($danisman->status ? 'aktif' : 'pasif');
            }

            // Şifre güncelleme
            if ($request->filled('password')) {
                $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            }

            // Context7: uzmanlik_alanlari JSON array olarak kaydet
            if ($request->has('uzmanlik_alanlari') && is_array($request->uzmanlik_alanlari)) {
                // Sadece izin verilen değerleri filtrele
                $allowedAreas = ['Konut', 'Arsa', 'İşyeri', 'Yazlık', 'Turistik Tesis'];
                $filteredAreas = array_filter($request->uzmanlik_alanlari, function ($area) use ($allowedAreas) {
                    return in_array($area, $allowedAreas);
                });
                if (! empty($filteredAreas)) {
                    $userData['uzmanlik_alanlari'] = array_values($filteredAreas); // JSON array olarak kaydet
                }
            }

            // Profil fotoğrafı yükleme
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $path = $photo->store('profile-photos', 'public');
                $userData['profile_photo_path'] = $path;
            }

            $danisman->update($userData);

            return redirect()
                ->route('admin.danisman.show', $danisman)
                ->with('success', 'Danışman bilgileri başarıyla güncellendi.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Danışman güncelleme hatası: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Danışman güncellenirken hata oluştu: '.$e->getMessage());
        }
    }

    public function destroy(User $danisman)
    {
        try {
            // Danışman bilgilerini al
            $danismanAdi = $danisman->name;

            // Danışmanın rolünü kontrol et (Context7: Spatie Permission kullanımı)
            if (! $danisman->hasRole('danisman')) {
                return redirect()
                    ->route('admin.danisman.index')
                    ->with('error', 'Bu kullanıcı bir danışman değil.');
            }

            $danisman->delete();

            return redirect()
                ->route('admin.danisman.index')
                ->with('success', $danismanAdi.' başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.danisman.index')
                ->with('error', 'Danışman silinirken bir hata oluştu: '.$e->getMessage());
        }
    }

    public function search(Request $request)
    {
        return response()->json(['items' => []]);
    }

    public function toggleStatus(User $danisman)
    {
        // Route model binding ile gelen $danisman otomatik olarak User modeli

        // Danışman rolünü kontrol et
        if (! $danisman->hasRole('danisman')) {
            return response()->json(['success' => false, 'message' => 'Bu kullanıcı bir danışman değil'], 404);
        }

        $danisman->status = ! $danisman->status;
        $danisman->save();

        return response()->json([
            'success' => true,
            'status' => $danisman->status,
            'message' => $danisman->status ? 'Danışman aktif edildi' : 'Danışman pasif edildi',
        ]);
    }

    public function updateOnlineStatus(User $danisman)
    {
        // Route model binding ile gelen $danisman otomatik olarak User modeli

        $danisman->last_activity_at = now();
        $danisman->save();

        return response()->json(['success' => true, 'message' => 'Online durum güncellendi']);
    }

    public function bulkAction(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function performanceReport(Request $request)
    {
        return response()->json(['success' => true, 'report' => []]);
    }

    private function renderIfExists(string $view, array $data): Response|\Illuminate\Contracts\View\View
    {
        if (view()->exists($view)) {
            return response()->view($view, $data);
        }

        return response('Danışman sayfaları hazır değil', 200);
    }

    private function resolveDanisman($danisman): User
    {
        if ($danisman instanceof User) {
            return $danisman;
        }

        // ✅ FIX: Context7 - Spatie Permission uses 'roles' (plural), not 'role'
        return User::whereKey($danisman)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'danisman');
            })
            ->firstOrFail();
    }
}
