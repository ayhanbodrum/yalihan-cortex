<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");

        return count($indexes) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // İlanlar tablosuna eksik index'leri ekle (sadece eksik olanları)
        Schema::table('ilanlar', function (Blueprint $table) {
            // Sadece eksik index'leri ekle
            if (! $this->indexExists('ilanlar', 'idx_ilanlar_mahalle')) {
                $table->index('mahalle_id', 'idx_ilanlar_mahalle');
            }
            if (! $this->indexExists('ilanlar', 'idx_ilanlar_fiyat')) {
                $table->index('fiyat', 'idx_ilanlar_fiyat');
            }
            if (! $this->indexExists('ilanlar', 'idx_ilanlar_updated_at')) {
                $table->index('updated_at', 'idx_ilanlar_updated_at');
            }
            if (! $this->indexExists('ilanlar', 'idx_ilanlar_fiyat_status')) {
                $table->index(['fiyat', 'status'], 'idx_ilanlar_fiyat_status');
            }
        });

        // İlan kategorileri tablosuna index'ler ekle
        Schema::table('ilan_kategorileri', function (Blueprint $table) {
            if (! $this->indexExists('ilan_kategorileri', 'idx_ilan_kategorileri_parent')) {
                $table->index('parent_id', 'idx_ilan_kategorileri_parent');
            }
            if (! $this->indexExists('ilan_kategorileri', 'idx_ilan_kategorileri_status')) {
                $table->index('status', 'idx_ilan_kategorileri_status');
            }
            // ✅ CONTEXT7: display_order kolonu migration ile eklenmiş olabilir, kontrol et
            if (Schema::hasColumn('ilan_kategorileri', 'display_order')) {
                if (! $this->indexExists('ilan_kategorileri', 'idx_ilan_kategorileri_display_order')) {
                    try {
                        $table->index('display_order', 'idx_ilan_kategorileri_display_order'); // Context7: order → display_order
                    } catch (\Exception $e) {
                        // Index zaten varsa veya kolon yoksa skip et
                        if (
                            strpos($e->getMessage(), 'Duplicate key name') === false &&
                            strpos($e->getMessage(), "doesn't exist") === false
                        ) {
                            throw $e;
                        }
                    }
                }
            }
        });

        // Kişiler tablosuna index'ler ekle
        Schema::table('kisiler', function (Blueprint $table) {
            if (! $this->indexExists('kisiler', 'idx_kisiler_il')) {
                $table->index('il_id', 'idx_kisiler_il');
            }
            if (! $this->indexExists('kisiler', 'idx_kisiler_ilce')) {
                $table->index('ilce_id', 'idx_kisiler_ilce');
            }
            if (! $this->indexExists('kisiler', 'idx_kisiler_status')) {
                $table->index('status', 'idx_kisiler_status');
            }
            if (! $this->indexExists('kisiler', 'idx_kisiler_created_at')) {
                $table->index('created_at', 'idx_kisiler_created_at');
            }
        });

        // Özellikler tablosuna index'ler ekle
        Schema::table('ozellikler', function (Blueprint $table) {
            if (! $this->indexExists('ozellikler', 'idx_ozellikler_kategori')) {
                $table->index('kategori_id', 'idx_ozellikler_kategori');
            }
            if (! $this->indexExists('ozellikler', 'idx_ozellikler_status')) {
                $table->index('status', 'idx_ozellikler_status');
            }
            // ✅ CONTEXT7: display_order kolonu migration ile eklenmiş olabilir, kontrol et
            if (Schema::hasColumn('ozellikler', 'display_order')) {
                if (! $this->indexExists('ozellikler', 'idx_ozellikler_display_order')) {
                    try {
                        $table->index('display_order', 'idx_ozellikler_display_order'); // Context7: order → display_order
                    } catch (\Exception $e) {
                        // Index zaten varsa veya kolon yoksa skip et
                        if (
                            strpos($e->getMessage(), 'Duplicate key name') === false &&
                            strpos($e->getMessage(), "doesn't exist") === false
                        ) {
                            throw $e;
                        }
                    }
                }
            }
        });

        // AI logs tablosuna index'ler ekle
        Schema::table('ai_logs', function (Blueprint $table) {
            if (! $this->indexExists('ai_logs', 'idx_ai_logs_user')) {
                $table->index('user_id', 'idx_ai_logs_user');
            }
            if (! $this->indexExists('ai_logs', 'idx_ai_logs_provider')) {
                $table->index('provider', 'idx_ai_logs_provider');
            }
            if (! $this->indexExists('ai_logs', 'idx_ai_logs_created_at')) {
                $table->index('created_at', 'idx_ai_logs_created_at');
            }
            if (! $this->indexExists('ai_logs', 'idx_ai_logs_provider_created')) {
                $table->index(['provider', 'created_at'], 'idx_ai_logs_provider_created');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // İlanlar tablosundan index'leri kaldır
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropIndex('idx_ilanlar_ana_kategori');
            $table->dropIndex('idx_ilanlar_alt_kategori');
            $table->dropIndex('idx_ilanlar_yayin_tipi');
            $table->dropIndex('idx_ilanlar_il');
            $table->dropIndex('idx_ilanlar_ilce');
            $table->dropIndex('idx_ilanlar_mahalle');
            $table->dropIndex('idx_ilanlar_fiyat');
            $table->dropIndex('idx_ilanlar_status');
            $table->dropIndex('idx_ilanlar_created_at');
            $table->dropIndex('idx_ilanlar_updated_at');
            $table->dropIndex('idx_ilanlar_il_ilce');
            $table->dropIndex('idx_ilanlar_kategori_status');
            $table->dropIndex('idx_ilanlar_fiyat_status');
        });

        // İlan kategorileri tablosundan index'leri kaldır
        Schema::table('ilan_kategorileri', function (Blueprint $table) {
            $table->dropIndex('idx_ilan_kategorileri_parent');
            $table->dropIndex('idx_ilan_kategorileri_status');
            $table->dropIndex('idx_ilan_kategorileri_sira');
        });

        // Kişiler tablosundan index'leri kaldır
        Schema::table('kisiler', function (Blueprint $table) {
            $table->dropIndex('idx_kisiler_il');
            $table->dropIndex('idx_kisiler_ilce');
            $table->dropIndex('idx_kisiler_status');
            $table->dropIndex('idx_kisiler_created_at');
        });

        // Özellikler tablosundan index'leri kaldır
        Schema::table('ozellikler', function (Blueprint $table) {
            $table->dropIndex('idx_ozellikler_kategori');
            $table->dropIndex('idx_ozellikler_status');
            $table->dropIndex('idx_ozellikler_sira');
        });

        // AI logs tablosundan index'leri kaldır
        Schema::table('ai_logs', function (Blueprint $table) {
            $table->dropIndex('idx_ai_logs_user');
            $table->dropIndex('idx_ai_logs_provider');
            $table->dropIndex('idx_ai_logs_created_at');
            $table->dropIndex('idx_ai_logs_provider_created');
        });
    }
};
