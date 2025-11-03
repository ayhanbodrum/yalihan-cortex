<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
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
        $query = User::with('roles');

        // Context7 compliance: Use 'status' field instead of 'status'
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Active users by default (Context7: status = 1)
        if (!$request->has('status')) {
            $query->where('status', 1);
        }

        $users = $query->paginate(20);

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
            'telefon' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,danisman,editor,viewer',
            'enabled' => 'nullable|boolean',
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
            'telefon' => $request->telefon,
            'password' => Hash::make($request->password),
            'enabled' => $request->get('enabled', true),
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
        return view('admin.users.edit', ['user' => $kullanicilar]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $kullanicilar)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $kullanicilar->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'boolean', // Context7: status instead of status
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->get('status', 1), // Context7 compliant
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $kullanicilar->update($updateData);

        // Update role using Spatie Permission
        if ($request->role_id) {
            // Remove existing roles
            $kullanicilar->syncRoles([]);
            
            // Assign new role based on role_id
            $roleMap = [
                1 => 'super_admin',
                2 => 'admin', 
                3 => 'danisman',
                4 => 'user'
            ];
            
            if (isset($roleMap[$request->role_id])) {
                $kullanicilar->assignRole($roleMap[$request->role_id]);
            }
        }

        return redirect()->route('admin.kullanicilar.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
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
            'status' => !$kullanicilar->status // Context7: toggle status field
        ]);

        return response()->json([
            'success' => true,
            'status' => $kullanicilar->status,
            'message' => 'Kullanıcı statusu güncellendi.'
        ]);
    }
}
