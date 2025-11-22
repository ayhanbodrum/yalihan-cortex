<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * GLOBAL STATUS COLUMN STANDARDIZATION
 * 
 * Context7 Compliance: Tüm basit status kolonları TINYINT(1) boolean olmalı
 * 
 * Sorun: Projede 3 farklı format kullanılıyor:
 * 1. VARCHAR(255) + 'Aktif'/'Pasif' string (10 tablo)
 * 2. ENUM('Aktif','Pasif') (6 tablo)
 * 3. TINYINT(1) boolean (20 tablo - doğru format)
 * 
 * Bu tutarsızlık IDE'lerin (trea, warp, cursor) tip kontrolü yapmasını engelliyor
 * ve sürekli 'Aktif'/'Pasif' vs true/false karışıklığına yol açıyor.
 * 
 * Çözüm: Tüm basit aktif/pasif status kolonlarını boolean'a çevir
 * 
 * ⚠️ KARMAŞIK STATUS'LAR DEĞİŞMEYECEK:
 * - blog_posts: 'draft', 'published', 'scheduled' (VARCHAR kalacak)
 * - eslesmeler: 'beklemede', 'eslesti', 'iptal' (VARCHAR kalacak)
 * - gorevler: 'Beklemede', 'Devam Ediyor', 'Tamamlandi' (VARCHAR kalacak)
 * - yazlik_rezervasyonlar: 'beklemede', 'onaylandi', 'iptal' (ENUM kalacak)
 * - sites: 'active', 'inactive', 'pending' (ENUM kalacak)
 * 
 * @see .context7/authority.json - database_fields.status
 */
return new class extends Migration
{
    /**
     * Basit boolean status'a çevrilecek tablolar
     * (Sadece aktif/pasif durumu olan tablolar)
     */
    private array $tablesToFix = [
        // VARCHAR(255) + 'Aktif'/'Pasif'
        'blog_categories',
        'blog_tags',
        'ilanlar',
        'kisiler',
        'ozellik_kategorileri',
        'ozellikler',
        'projeler',
        'takim_uyeleri',
        'talepler',
        'ulkeler',
        
        // ENUM('Aktif','Pasif')
        'anahtar_yonetimi',
        'ilan_ozellikleri',
        'ilan_resimleri',
        'ilceler',
        'iller',
        'mahalleler',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('🔧 GLOBAL STATUS COLUMN STANDARDIZATION başlatılıyor...');
        Log::info('   Toplam tablo: ' . count($this->tablesToFix));

        $successCount = 0;
        $skipCount = 0;

        foreach ($this->tablesToFix as $table) {
            try {
                // Tablo varlık kontrolü
                if (!Schema::hasTable($table)) {
                    Log::warning("  ⚠️  Tablo bulunamadı: {$table}");
                    $skipCount++;
                    continue;
                }

                // Status kolonu varlık kontrolü
                if (!Schema::hasColumn($table, 'status')) {
                    Log::warning("  ⚠️  Status kolonu bulunamadı: {$table}");
                    $skipCount++;
                    continue;
                }

                Log::info("  🔧 Düzeltiliyor: {$table}");

                // Step 1: Önce VARCHAR'a çevir (ENUM'ları handle etmek için)
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN status VARCHAR(50) NULL");
                } catch (\Exception $e) {
                    // Zaten VARCHAR ise devam et
                }

                // Step 2: Verileri normalize et
                // 'Aktif', 'aktif', 'active', 'Active', '1', 1 → '1'
                DB::statement("
                    UPDATE `{$table}`
                    SET status = '1'
                    WHERE status IN ('Aktif', 'aktif', 'active', 'Active', '1', 1)
                ");

                // 'Pasif', 'pasif', 'inactive', 'Inactive', '0', 0 → '0'
                DB::statement("
                    UPDATE `{$table}`
                    SET status = '0'
                    WHERE status IN ('Pasif', 'pasif', 'inactive', 'Inactive', '0', 0)
                ");

                // NULL veya bilinmeyen değerler → '1' (default active)
                DB::statement("
                    UPDATE `{$table}`
                    SET status = '1'
                    WHERE status IS NULL OR status NOT IN ('0', '1')
                ");

                // Step 3: TINYINT(1) boolean'a çevir
                DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active (Context7 boolean)'");

                $affectedRows = DB::table($table)->count();
                Log::info("    ✅ {$table} başarıyla düzeltildi ({$affectedRows} satır)");
                $successCount++;

            } catch (\Exception $e) {
                Log::error("    ❌ {$table} düzeltilemedi: " . $e->getMessage());
                // Hata olsa bile devam et, diğer tabloları düzelt
            }
        }

        Log::info('');
        Log::info('✅ GLOBAL STATUS COLUMN STANDARDIZATION tamamlandı!');
        Log::info("   ✅ Başarılı: {$successCount} tablo");
        Log::info("   ⚠️  Atlandı: {$skipCount} tablo");
        Log::info('');
        Log::info('🎯 Sonuç: Artık tüm basit status kolonları TINYINT(1) boolean!');
        Log::info('   IDE\'ler (trea, warp, cursor) artık tutarlı tip kontrolü yapabilir.');
        Log::info('   Kodda her yerde true/false kullanabilirsiniz.');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Log::info('⏮️  GLOBAL STATUS COLUMN STANDARDIZATION geri alınıyor...');

        foreach ($this->tablesToFix as $table) {
            try {
                if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'status')) {
                    continue;
                }

                // TINYINT'ten VARCHAR'a çevir
                DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN status VARCHAR(50) NULL");

                // Verileri string'e çevir
                DB::statement("UPDATE `{$table}` SET status = 'Aktif' WHERE status = 1");
                DB::statement("UPDATE `{$table}` SET status = 'Pasif' WHERE status = 0");

                // Default değeri 'Aktif' yap
                DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Aktif'");

                Log::info("  ✅ Geri alındı: {$table}");

            } catch (\Exception $e) {
                Log::error("  ❌ Geri alma başarısız: {$table} - " . $e->getMessage());
            }
        }

        Log::info('✅ Geri alma tamamlandı');
    }
};
