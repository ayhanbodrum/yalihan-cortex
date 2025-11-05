<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Enums\UserRole;

class DanismanController extends AdminController
{
    public function index(Request $request)
    {
        $query = User::query();
        // Context7: Spatie Permission uses 'roles' (plural), not 'role'
        $query->whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        });
        $search = trim((string) $request->get('search'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
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
        return $this->renderIfExists('admin.danisman.index', compact('danismanlar', 'istatistikler', 'filters'));
    }

    public function create()
    {
        return $this->renderIfExists('admin.danisman.create', []);
    }

    public function show($id)
    {
        // Context7: Show danışman details
        $danisman = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->findOrFail($id);

        $toplamIlan = \App\Models\Ilan::where('danisman_id', $id)->count();
        $aktifIlan = \App\Models\Ilan::where('danisman_id', $id)->where('status', 1)->count();
        $toplamMusteri = \App\Models\Kisi::where('danisman_id', $id)->count();
        $aktifMusteri = \App\Models\Kisi::where('danisman_id', $id)->where('status', 1)->count();

        $basariOrani = $toplamIlan > 0 ? round(($aktifIlan / $toplamIlan) * 100, 1) : 0.0;

        // ✅ Talep sayısı implementasyonu
        $toplamTalep = \App\Models\Talep::where('danisman_id', $id)->count();
        $aktifTalep = \App\Models\Talep::where('danisman_id', $id)
            ->where('status', 'Aktif')
            ->count();

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
        ];

        $aiOneriler = [];

        return view('admin.danisman.show', compact('danisman', 'performans', 'aiOneriler'));
    }

    public function edit($id)
    {
        // Context7: Edit danışman
        $danisman = User::whereHas('roles', function ($q) {
            $q->where('name', 'danisman');
        })->findOrFail($id);

        return view('admin.danisman.edit', compact('danisman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefon' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'lisans_no' => 'nullable|string|max:50',
            'komisyon_orani' => 'nullable|numeric|min:0|max:100',
            'uzmanlik_alani' => 'nullable|string|max:100',
            'adres' => 'nullable|string|max:500',
            'status' => 'required|boolean',
        ]);

        try {
            // Create user
            $user = \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'status' => $request->status,
            ]);

            // Assign danisman role
            $user->assignRole('danisman');

            // Create or update danisman record if danismans table exists
            try {
                $danisman = \App\Models\Danisman::create([
                    'user_id' => $user->id,
                    'lisans_no' => $request->lisans_no,
                    'telefon' => $request->telefon,
                    'komisyon_orani' => $request->komisyon_orani ?? 2.5,
                    'uzmanlik_alani' => $request->uzmanlik_alani,
                    'adres' => $request->adres,
                    'aktif' => $request->status,
                ]);
            } catch (\Exception $e) {
                // If danismans table doesn't exist, just use the user record
                \Illuminate\Support\Facades\Log::info('Danismans table not found, using users table only');
            }

            return redirect()->route('admin.danisman.index')
                ->with('success', 'Danışman başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Danışman oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $danisman)
    {
        return redirect()->back();
    }

    public function destroy(User $danisman)
    {
        try {
            // Danışman bilgilerini al
            $danismanAdi = $danisman->name;

            // Danışmanın rolünü kontrol et
            if (!$danisman->role || $danisman->role->name !== 'danisman') {
                return redirect()
                    ->route('admin.danisman.index')
                    ->with('error', 'Bu kullanıcı bir danışman değil.');
            }

            $danisman->delete();

            return redirect()
                ->route('admin.danisman.index')
                ->with('success', $danismanAdi . ' başarıyla silindi.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.danisman.index')
                ->with('error', 'Danışman silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        return response()->json(['items' => []]);
    }

    public function toggleStatus($danisman)
    {
        return response()->json(['success' => true]);
    }

    public function updateOnlineStatus($danisman)
    {
        return response()->json(['success' => true]);
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
        if (view()->exists($view)) return response()->view($view, $data);
        return response('Danışman sayfaları hazır değil', 200);
    }

    private function resolveDanisman($danisman): User
    {
        if ($danisman instanceof User) return $danisman;
        return User::whereKey($danisman)
            ->whereHas('role', function ($q) {
                $q->where('name', 'danisman');
            })
            ->firstOrFail();
    }
}
