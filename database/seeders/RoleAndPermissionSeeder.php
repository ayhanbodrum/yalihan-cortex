<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Önce foreign key check'i kapat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Mevcut kayıtları temizle
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // Foreign key check'i aç
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Yetkiler
        $permissions = [
            'ayarları yönet',
            'kullanıcıları yönet',
            'danışmanları yönet',
            'ilanları yönet',
            'görüntüleyebilir',
            'düzenleyebilir',
            'silebilir',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Roller
        $roleData = [
            ['name' => 'Süper Admin', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Danışman', 'guard_name' => 'web'],
        ];

        foreach ($roleData as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Süper Admin tüm yetkilere sahip
        $superAdminId = DB::table('roles')->where('name', 'Süper Admin')->first()->id;
        $allPermissions = DB::table('permissions')->get();
        foreach ($allPermissions as $permission) {
            DB::table('role_has_permissions')->insert([
                'permission_id' => $permission->id,
                'role_id' => $superAdminId,
            ]);
        }

        // Admin bazı yetkilere sahip
        $adminId = DB::table('roles')->where('name', 'Admin')->first()->id;
        $adminPermissions = ['kullanıcıları yönet', 'danışmanları yönet', 'ilanları yönet', 'görüntüleyebilir', 'düzenleyebilir'];
        foreach ($adminPermissions as $permissionName) {
            $permission = DB::table('permissions')->where('name', $permissionName)->first();
            if ($permission) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $adminId,
                ]);
            }
        }

        // Danışman sadece görüntüleyebilir ve düzenleyebilir
        $danismanId = DB::table('roles')->where('name', 'Danışman')->first()->id;
        $danismanPermissions = ['görüntüleyebilir', 'düzenleyebilir'];
        foreach ($danismanPermissions as $permissionName) {
            $permission = DB::table('permissions')->where('name', $permissionName)->first();
            if ($permission) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permission->id,
                    'role_id' => $danismanId,
                ]);
            }
        }
    }
}
