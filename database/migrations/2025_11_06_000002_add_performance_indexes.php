<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Performance Optimization: Add missing database indexes
     * 
     * Estimated Performance Gain: 50-70% faster queries
     * Impact: HIGH - All listing, customer, and request pages
     */
    public function up(): void
    {
        // İlanlar table indexes
        Schema::table('ilanlar', function (Blueprint $table) {
            // Status index (Context7: standardized naming)
            if (!$this->indexExists('ilanlar', 'idx_ilanlar_status')) {
                $table->index('status', 'idx_ilanlar_status');
            }
            
            // Category index (Context7: standardized naming)
            if (!$this->indexExists('ilanlar', 'idx_ilanlar_ana_kategori')) {
                $table->index('ana_kategori_id', 'idx_ilanlar_ana_kategori');
            }
            
            // Location composite index (Context7: standardized naming)
            if (!$this->indexExists('ilanlar', 'idx_ilanlar_location')) {
                $table->index(['il_id', 'ilce_id', 'mahalle_id'], 'idx_ilanlar_location');
            }
            
            // Agent index (Context7: standardized naming)
            if (!$this->indexExists('ilanlar', 'idx_ilanlar_danisman')) {
                $table->index('danisman_id', 'idx_ilanlar_danisman');
            }
            
            // Price index (Context7: standardized naming)
            if (!$this->indexExists('ilanlar', 'idx_ilanlar_fiyat')) {
                $table->index('fiyat', 'idx_ilanlar_fiyat');
            }
            
            // Created date index (Context7: standardized naming)
            if (!$this->indexExists('ilanlar', 'idx_ilanlar_created_at')) {
                $table->index('created_at', 'idx_ilanlar_created_at');
            }
        });

        // Kisiler table indexes
        Schema::table('kisiler', function (Blueprint $table) {
            // Status index
            if (!$this->indexExists('kisiler', 'kisiler_status_index')) {
                $table->index('status', 'kisiler_status_index');
            }
            
            // Agent index
            if (!$this->indexExists('kisiler', 'kisiler_danisman_id_index')) {
                $table->index('danisman_id', 'kisiler_danisman_id_index');
            }
            
            // Location index
            if (!$this->indexExists('kisiler', 'kisiler_location_index')) {
                $table->index(['il_id', 'ilce_id'], 'kisiler_location_index');
            }
            
            // Phone index (for quick search)
            if (!$this->indexExists('kisiler', 'kisiler_telefon_index')) {
                $table->index('telefon', 'kisiler_telefon_index');
            }
        });

        // Talepler table indexes
        Schema::table('talepler', function (Blueprint $table) {
            // Status index
            if (!$this->indexExists('talepler', 'talepler_status_index')) {
                $table->index('status', 'talepler_status_index');
            }
            
            // Kisi index
            if (!$this->indexExists('talepler', 'talepler_kisi_id_index')) {
                $table->index('kisi_id', 'talepler_kisi_id_index');
            }
            
            // Created date index
            if (!$this->indexExists('talepler', 'talepler_created_at_index')) {
                $table->index('created_at', 'talepler_created_at_index');
            }
        });

        // Yazlik Rezervasyonlar indexes
        if (Schema::hasTable('yazlik_rezervasyonlar')) {
            Schema::table('yazlik_rezervasyonlar', function (Blueprint $table) {
                // Date range index (for availability search)
                if (!$this->indexExists('yazlik_rezervasyonlar', 'yazlik_rezervasyonlar_dates_index')) {
                    $table->index(['check_in', 'check_out'], 'yazlik_rezervasyonlar_dates_index');
                }
                
                // İlan index
                if (!$this->indexExists('yazlik_rezervasyonlar', 'yazlik_rezervasyonlar_ilan_id_index')) {
                    $table->index('ilan_id', 'yazlik_rezervasyonlar_ilan_id_index');
                }
                
                // Status index
                if (!$this->indexExists('yazlik_rezervasyonlar', 'yazlik_rezervasyonlar_status_index')) {
                    $table->index('status', 'yazlik_rezervasyonlar_status_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // İlanlar indexes
        Schema::table('ilanlar', function (Blueprint $table) {
            $table->dropIndex('idx_ilanlar_status');
            $table->dropIndex('idx_ilanlar_ana_kategori');
            $table->dropIndex('idx_ilanlar_location');
            $table->dropIndex('idx_ilanlar_danisman');
            $table->dropIndex('idx_ilanlar_fiyat');
            $table->dropIndex('idx_ilanlar_created_at');
        });

        // Kisiler indexes
        Schema::table('kisiler', function (Blueprint $table) {
            $table->dropIndex('kisiler_status_index');
            $table->dropIndex('kisiler_danisman_id_index');
            $table->dropIndex('kisiler_location_index');
            $table->dropIndex('kisiler_telefon_index');
        });

        // Talepler indexes
        Schema::table('talepler', function (Blueprint $table) {
            $table->dropIndex('talepler_status_index');
            $table->dropIndex('talepler_kisi_id_index');
            $table->dropIndex('talepler_created_at_index');
        });

        // Yazlik Rezervasyonlar indexes
        if (Schema::hasTable('yazlik_rezervasyonlar')) {
            Schema::table('yazlik_rezervasyonlar', function (Blueprint $table) {
                $table->dropIndex('yazlik_rezervasyonlar_dates_index');
                $table->dropIndex('yazlik_rezervasyonlar_ilan_id_index');
                $table->dropIndex('yazlik_rezervasyonlar_status_index');
            });
        }
    }
    
    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);
            
        return isset($indexes[$index]);
    }
};

