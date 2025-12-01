<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends AdminController
{
    /**
     * Display a listing of users
     * Context7 compliant: uses 'status' instead of 'status'
     */
    public function index(Request $request)
    {
        // ✅ N+1 FIX: Eager loading with select optimization
        $query = User::select(['id', 'name', 'email', 'status', 'email_verified_at', 'created_at', 'updated_at'])
            ->with(['roles:id,name']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter (Spatie Permission)
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Status filter (Context7 compliance)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Active users by default (Context7: status = 1)
            $query->where('status', 1);
        }

        // Sorting
        $sort = $request->get('sort');
        if ($sort === 'id_asc') {
            $query->orderBy('id', 'asc');
        } elseif ($sort === 'id_desc') {
            $query->orderBy('id', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'date_desc') {
            $query->orderBy('created_at', 'desc');
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:superadmin,admin,danisman,editor,musteri',
            'status' => 'nullable|boolean', // Context7: enabled → status
            'email_verified' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'status' => $request->get('status', true), // Context7: enabled → status
            'email_verified_at' => $request->get('email_verified') ? now() : null,
        ]);

        // Assign role using Spatie Permission
        if ($request->role) {
            $user->assignRole($request->role);
        }

        return redirect()->route('admin.kullanicilar.index')
            ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $kullanicilar)
    {
        // ✅ Context7: Load roles for dropdown
        $roles = \Spatie\Permission\Models\Role::all(['id', 'name']);

        return view('admin.users.edit', [
            'user' => $kullanicilar,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user
     * ✅ Context7: Kullanıcı güncelleme - flash mesaj ve validation düzeltmeleri
     */
    public function update(Request $request, User $kullanicilar)
    {
        // ✅ Context7: Validation - status string olarak geliyor (0 veya 1)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$kullanicilar->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:superadmin,admin,danisman,editor,musteri', // ✅ Context7: Rol zorunlu
            'status' => 'nullable|in:0,1', // ✅ Context7: status string olarak (0 veya 1)
        ], [
            'role.required' => 'Kullanıcı rolü seçilmelidir.',
            'role.in' => 'Geçersiz rol seçildi. Lütfen geçerli bir rol seçin.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Lütfen form hatalarını düzeltin.'); // ✅ Context7: Error flash mesajı
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->has('status') ? (int) $request->status : ($kullanicilar->status ?? 1), // ✅ Context7: status field (0 or 1)
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        try {
            $kullanicilar->update($updateData);

            // ✅ Context7: Update role using Spatie Permission
            $roleUpdated = false;
            if ($request->filled('role') && ! empty($request->role)) {
                try {
                    // Mevcut rolü kontrol et
                    $currentRole = $kullanicilar->getRoleNames()->first();
                    $newRole = $request->role;

                    // Rol değişmişse veya hiç rol yoksa güncelle
                    if ($currentRole !== $newRole) {
                        // Remove existing roles and assign new role
                        $kullanicilar->syncRoles([$newRole]);

                        // ✅ Context7: Cache'i temizle ve refresh et
                        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
                        $kullanicilar->refresh();

                        $roleUpdated = true;

                        // Log role assignment for debugging
                        \Illuminate\Support\Facades\Log::info('Kullanıcı rolü güncellendi', [
                            'user_id' => $kullanicilar->id,
                            'user_name' => $kullanicilar->name,
                            'old_role' => $currentRole ?? 'Yok',
                            'new_role' => $newRole,
                            'roles_after' => $kullanicilar->getRoleNames()->toArray(),
                            'has_role_check' => $kullanicilar->hasRole($newRole),
                        ]);
                    } else {
                        // Rol değişmemiş, sadece log
                        \Illuminate\Support\Facades\Log::info('Kullanıcı rolü değişmedi', [
                            'user_id' => $kullanicilar->id,
                            'user_name' => $kullanicilar->name,
                            'current_role' => $currentRole ?? 'Yok',
                        ]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Rol atama hatası', [
                        'user_id' => $kullanicilar->id,
                        'role' => $request->role,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    return redirect()->back()
                        ->with('error', 'Rol atama sırasında bir hata oluştu: '.$e->getMessage())
                        ->withInput();
                }
            } else {
                // ✅ Context7: Rol boşsa hata döndür (validation zaten kontrol ediyor ama ekstra güvenlik)
                return redirect()->back()
                    ->with('error', 'Kullanıcı rolü seçilmelidir.')
                    ->withInput();
            }

            // ✅ Context7: Success message with role info
            $successMessage = 'Kullanıcı başarıyla güncellendi.';
            if ($roleUpdated) {
                $successMessage .= ' Rol: '.ucfirst($request->role);
            }

            return redirect()->route('admin.kullanicilar.edit', $kullanicilar->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Kullanıcı güncelleme hatası', [
                'user_id' => $kullanicilar->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Kullanıcı güncellenirken bir hata oluştu: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $kullanicilar)
    {
        $kullanicilar->delete();

        return redirect()->route('admin.kullanicilar.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }

    /**
     * Toggle user status (Context7 compliant)
     */
    public function toggleStatus(User $kullanicilar)
    {
        $kullanicilar->update([
            'status' => ! $kullanicilar->status, // Context7: toggle status field
        ]);

        return response()->json([
            'success' => true,
            'status' => $kullanicilar->status,
            'message' => 'Kullanıcı statusu güncellendi.',
        ]);
    }
}
