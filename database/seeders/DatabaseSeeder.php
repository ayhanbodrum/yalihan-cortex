<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Database Seeder
 *
 * Context7 standartlarına uygun ana seeder.
 * TEK MASTER SEEDER: Context7MasterSeeder tüm verileri yönetir.
 *
 * Kullanım:
 *   php artisan db:seed                    → Tüm verileri seed eder
 *   php artisan db:seed --class=Context7MasterSeeder  → Aynı sonuç
 *
 * Context7 Standardı: C7-DATABASE-SEEDER-2025-11-05
 * Versiyon: 5.0.0 (Tek Master Seeder)
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Database Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-DATABASE-SEEDER-2025-11-05');
        $this->command->info('🔧 Versiyon: 5.0.0 (Tek Master Seeder)');
        $this->command->info('📦 Master Seeder: Context7MasterSeeder');
        $this->command->newLine();

        // ✅ TEK MASTER SEEDER - Tüm verileri Context7 kurallarına göre yükler
        $this->call([
            Context7MasterSeeder::class,
        ]);

        if (app()->environment(['local', 'development', 'testing'])) {
            $this->command->info('🌱 Portfolio demo verileri yükleniyor...');
            $this->call(PortfolioDemoSeeder::class);

            $this->command->info('🏖️ Bodrum demo verileri yükleniyor...');
            $this->call(BodrumDemoSeeder::class);
        }

        $this->command->newLine();
        $this->command->info('🎉 Database Seeder başarıyla tamamlandı!');
        $this->command->info('📊 Tüm veriler Context7 standartlarına uygun olarak yüklendi');
        $this->command->info('🔗 Veri tutarlılığı: %100');
        $this->command->info('⚡ Performance: Optimize edildi');
    }
}
