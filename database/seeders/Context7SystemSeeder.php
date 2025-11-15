<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Context7 System Seeder
 *
 * Context7 standartlarına uygun sistem verileri.
 * Roller, kullanıcılar, yetkiler ve temel sistem verilerini oluşturur.
 *
 * Context7 Standardı: C7-SYSTEM-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class Context7SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔧 Context7 Sistem Verileri oluşturuluyor...');

        // 1. Roller oluştur
        $this->createRoles();

        // 2. Admin kullanıcıları oluştur
        $this->createAdminUsers();

        // 3. Danışman kullanıcıları oluştur
        $this->createConsultantUsers();

        // 4. Uzmanlık alanları oluştur (geçici olarak devre dışı)
        // $this->createExpertiseAreas();

        // 5. Kullanıcı uzmanlık ilişkileri oluştur (geçici olarak devre dışı)
        // $this->createUserExpertise();

        $this->command->info('✅ Context7 sistem verileri başarıyla oluşturuldu!');
    }

    /**
     * Roller oluştur
     */
    private function createRoles(): void
    {
        $this->command->info('👑 Roller oluşturuluyor...');

        // Spatie Role format için basit roller
        $roles = [
            ['name' => 'superadmin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'danisman', 'guard_name' => 'web'],
            ['name' => 'editor', 'guard_name' => 'web'],
            ['name' => 'musteri', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('✅ ' . count($roles) . ' rol oluşturuldu');
    }

    /**
     * Admin kullanıcıları oluştur
     */
    private function createAdminUsers(): void
    {
        $this->command->info('👨‍💼 Admin kullanıcıları oluşturuluyor...');

        $adminUsers = [
            [
                'name' => 'Yalıhan Emlak',
                'email' => 'yalihanemlak@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => true,
                'email_verified_at' => now(),
                'role_id' => 1, // Süper Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ayhan Küçük',
                'email' => 'ayhankucuk@gmail.com',
                'password' => Hash::make('admin123'),
                'status' => true,
                'email_verified_at' => now(),
                'role_id' => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($adminUsers as $user) {
            $existingUser = DB::table('users')->where('email', $user['email'])->first();

            if (!$existingUser) {
                $userId = DB::table('users')->insertGetId($user);

                // Role assignment (simplified for now)
                // Spatie role assignment will be handled by the model
            }
        }

        $this->command->info('✅ Admin kullanıcıları oluşturuldu');
    }

    /**
     * Danışman kullanıcıları oluştur
     */
    private function createConsultantUsers(): void
    {
        $this->command->info('👨‍💼 Danışman kullanıcıları oluşturuluyor...');

        $consultantUsers = [
            [
                'name' => 'Yunus Emre Gök',
                'email' => 'y.emreyalihan@gmail.com',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
                'role_id' => 3, // Danışman
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Atılay Önen',
                'email' => 'atilay.onenn@gmail.com',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
                'role_id' => 3, // Danışman
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yeliz Tan Küçük',
                'email' => 'yeliztankucuk@gmail.com',
                'password' => Hash::make('password123'),
                'status' => true,
                'email_verified_at' => now(),
                'role_id' => 4, // Editör
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($consultantUsers as $user) {
            $existingUser = DB::table('users')->where('email', $user['email'])->first();

            if (!$existingUser) {
                $userId = DB::table('users')->insertGetId($user);

                // Role assignment (simplified for now)
                // Spatie role assignment will be handled by the model
            }
        }

        $this->command->info('✅ Danışman kullanıcıları oluşturuldu');
    }

    /**
     * Uzmanlık alanları oluştur
     */
    private function createExpertiseAreas(): void
    {
        $this->command->info('🎯 Uzmanlık alanları oluşturuluyor...');

        $expertiseAreas = [
            [
                'name' => 'Konut',
                'slug' => 'konut',
                'description' => 'Daire, villa, müstakil ev satış ve kiralama',
                'icon' => 'home',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'İş Yeri',
                'slug' => 'is-yeri',
                'description' => 'Ofis, dükkan, mağaza, depo satış ve kiralama',
                'icon' => 'building',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Arsa',
                'slug' => 'arsa',
                'description' => 'İmarlı arsa, tarla, yatırım arazisi',
                'icon' => 'map',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Turistik Tesis',
                'slug' => 'turistik-tesis',
                'description' => 'Otel, pansiyon, tatil köyü, yazlık',
                'icon' => 'sun',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lüks Konut',
                'slug' => 'luks-konut',
                'description' => 'Rezidans, penthouse, lüks villa',
                'icon' => 'crown',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($expertiseAreas as $area) {
            DB::table('expertise_areas')->updateOrInsert(
                ['name' => $area['name']],
                $area
            );
        }

        $this->command->info('✅ ' . count($expertiseAreas) . ' uzmanlık alanı oluşturuldu');
    }

    /**
     * Kullanıcı uzmanlık ilişkileri oluştur
     */
    private function createUserExpertise(): void
    {
        $this->command->info('🔗 Kullanıcı uzmanlık ilişkileri oluşturuluyor...');

        // Yunus Emre Gök - Konut, Arsa, İş Yeri
        $yunusId = DB::table('users')->where('email', 'y.emreyalihan@gmail.com')->value('id');
        if ($yunusId) {
            $expertiseIds = DB::table('expertise_areas')->whereIn('name', ['Konut', 'Arsa', 'İş Yeri'])->pluck('id');
            foreach ($expertiseIds as $expertiseId) {
                DB::table('user_expertise')->updateOrInsert(
                    ['user_id' => $yunusId, 'expertise_area_id' => $expertiseId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Atılay Önen - Lüks Konut, Turistik Tesis
        $atilayId = DB::table('users')->where('email', 'atilay.onenn@gmail.com')->value('id');
        if ($atilayId) {
            $expertiseIds = DB::table('expertise_areas')->whereIn('name', ['Lüks Konut', 'Turistik Tesis'])->pluck('id');
            foreach ($expertiseIds as $expertiseId) {
                DB::table('user_expertise')->updateOrInsert(
                    ['user_id' => $atilayId, 'expertise_area_id' => $expertiseId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $this->command->info('✅ Kullanıcı uzmanlık ilişkileri oluşturuldu');
    }
}
