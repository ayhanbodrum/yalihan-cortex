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
            // Status index (for filtering active/inactive listings)
            if (!$this->indexExists('ilanlar', 'ilanlar_status_index')) {
                $table->index('status', 'ilanlar_status_index');
            }
            
            // Category indexes (for category filtering)
            if (!$this->indexExists('ilanlar', 'ilanlar_ana_kategori_id_index')) {
                $table->index('ana_kategori_id', 'ilanlar_ana_kategori_id_index');
            }
            
            // Location composite index (for location-based search)
            if (!$this->indexExists('ilanlar', 'ilanlar_location_index')) {
                $table->index(['il_id', 'ilce_id', 'mahalle_id'], 'ilanlar_location_index');
            }
            
            // Agent index (for agent dashboard)
            if (!$this->indexExists('ilanlar', 'ilanlar_danisman_id_index')) {
                $table->index('danisman_id', 'ilanlar_danisman_id_index');
            }
            
            // Price index (for price range filtering)
            if (!$this->indexExists('ilanlar', 'ilanlar_fiyat_index')) {
                $table->index('fiyat', 'ilanlar_fiyat_index');
            }
            
            // Created date index (for recent listings)
            if (!$this->indexExists('ilanlar', 'ilanlar_created_at_index')) {
                $table->index('created_at', 'ilanlar_created_at_index');
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
            $table->dropIndex('ilanlar_status_index');
            $table->dropIndex('ilanlar_ana_kategori_id_index');
            $table->dropIndex('ilanlar_location_index');
            $table->dropIndex('ilanlar_danisman_id_index');
            $table->dropIndex('ilanlar_fiyat_index');
            $table->dropIndex('ilanlar_created_at_index');
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

