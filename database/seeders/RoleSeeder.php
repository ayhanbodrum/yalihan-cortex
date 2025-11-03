<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roller zaten var mı kontrol et
        if (DB::table('roles')->count() === 0) {
            DB::table('roles')->insert([
                [
                    'name' => 'admin',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'danisman',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'editor',
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $this->command->info('Roller başarıyla oluşturuldu.');
        } else {
            $this->command->info('Roller zaten mevcut, işlem atlandı.');
        }
    }
}
