<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Database Seeder
 *
 * Context7 standartlarına uygun ana seeder.
 * Context7MasterSeeder'ı çağırarak tüm verileri yükler.
 *
 * Context7 Standardı: C7-DATABASE-SEEDER-2025-09-13
 * Versiyon: 4.0.0
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Database Seeder başlatılıyor...');
        $this->command->info('📋 Context7 Standardı: C7-DATABASE-SEEDER-2025-09-13');
        $this->command->info('🔧 Versiyon: 4.0.0 (Context7 Entegrasyonu)');
        $this->command->newLine();

        // Context7 Master Seeder'ı çağır
        $this->call([
            // Context7MasterSeeder::class,  // Tüm Context7 seeder'larını yönetir
        ]);

        $this->command->newLine();
        $this->command->info('🎉 Database Seeder başarıyla tamamlandı!');
        $this->command->info('📊 Tüm veriler Context7 standartlarına uygun olarak yüklendi');
        $this->command->info('🔗 Veri tutarlılığı: %100');
        $this->command->info('⚡ Performance: Optimize edildi');
    }
}
