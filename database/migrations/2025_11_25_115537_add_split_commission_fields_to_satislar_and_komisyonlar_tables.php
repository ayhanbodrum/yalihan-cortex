<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Split Commission System Migration
 *
 * Context7 Standardı: C7-SPLIT-COMMISSION-2025-11-25
 *
 * Çift danışman komisyon sistemi için gerekli alanları ekler
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // satislar tablosuna çift komisyon alanları ekle
        if (Schema::hasTable('satislar')) {
            Schema::table('satislar', function (Blueprint $table) {
                // Satıcı danışman alanları
                if (! Schema::hasColumn('satislar', 'satici_danisman_id')) {
                    $table->unsignedBigInteger('satici_danisman_id')->nullable()->after('danisman_id')
                        ->comment('Satıcı tarafındaki danışman ID');
                }

                if (! Schema::hasColumn('satislar', 'alici_danisman_id')) {
                    $table->unsignedBigInteger('alici_danisman_id')->nullable()->after('satici_danisman_id')
                        ->comment('Alıcı tarafındaki danışman ID');
                }

                // Satıcı komisyon alanları
                if (! Schema::hasColumn('satislar', 'satici_komisyon_orani')) {
                    $table->decimal('satici_komisyon_orani', 5, 2)->nullable()->after('komisyon_orani')
                        ->comment('Satıcı danışmanı komisyon oranı (%)');
                }

                if (! Schema::hasColumn('satislar', 'alici_komisyon_orani')) {
                    $table->decimal('alici_komisyon_orani', 5, 2)->nullable()->after('satici_komisyon_orani')
                        ->comment('Alıcı danışmanı komisyon oranı (%)');
                }

                if (! Schema::hasColumn('satislar', 'satici_komisyon_tutari')) {
                    $table->decimal('satici_komisyon_tutari', 15, 2)->nullable()->after('komisyon_tutari')
                        ->comment('Satıcı danışmanı komisyon tutarı');
                }

                if (! Schema::hasColumn('satislar', 'alici_komisyon_tutari')) {
                    $table->decimal('alici_komisyon_tutari', 15, 2)->nullable()->after('satici_komisyon_tutari')
                        ->comment('Alıcı danışmanı komisyon tutarı');
                }
            });

            // Foreign key constraints ekle
            Schema::table('satislar', function (Blueprint $table) {
                if (
                    Schema::hasColumn('satislar', 'satici_danisman_id') &&
                    ! $this->foreignKeyExists('satislar', 'satislar_satici_danisman_id_foreign')
                ) {
                    $table->foreign('satici_danisman_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null')
                        ->onUpdate('cascade');
                }

                if (
                    Schema::hasColumn('satislar', 'alici_danisman_id') &&
                    ! $this->foreignKeyExists('satislar', 'satislar_alici_danisman_id_foreign')
                ) {
                    $table->foreign('alici_danisman_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null')
                        ->onUpdate('cascade');
                }
            });
        }

        // komisyonlar tablosuna çift komisyon alanları ekle
        if (Schema::hasTable('komisyonlar')) {
            Schema::table('komisyonlar', function (Blueprint $table) {
                // Satıcı danışman alanları
                if (! Schema::hasColumn('komisyonlar', 'satici_danisman_id')) {
                    $table->unsignedBigInteger('satici_danisman_id')->nullable()->after('danisman_id')
                        ->comment('Satıcı tarafındaki danışman ID');
                }

                if (! Schema::hasColumn('komisyonlar', 'alici_danisman_id')) {
                    $table->unsignedBigInteger('alici_danisman_id')->nullable()->after('satici_danisman_id')
                        ->comment('Alıcı tarafındaki danışman ID');
                }

                // Satıcı komisyon alanları
                if (! Schema::hasColumn('komisyonlar', 'satici_komisyon_orani')) {
                    $table->decimal('satici_komisyon_orani', 5, 2)->nullable()->after('komisyon_orani')
                        ->comment('Satıcı danışmanı komisyon oranı (%)');
                }

                if (! Schema::hasColumn('komisyonlar', 'alici_komisyon_orani')) {
                    $table->decimal('alici_komisyon_orani', 5, 2)->nullable()->after('satici_komisyon_orani')
                        ->comment('Alıcı danışmanı komisyon oranı (%)');
                }

                if (! Schema::hasColumn('komisyonlar', 'satici_komisyon_tutari')) {
                    $table->decimal('satici_komisyon_tutari', 15, 2)->nullable()->after('komisyon_tutari')
                        ->comment('Satıcı danışmanı komisyon tutarı');
                }

                if (! Schema::hasColumn('komisyonlar', 'alici_komisyon_tutari')) {
                    $table->decimal('alici_komisyon_tutari', 15, 2)->nullable()->after('satici_komisyon_tutari')
                        ->comment('Alıcı danışmanı komisyon tutarı');
                }
            });

            // Foreign key constraints ekle
            Schema::table('komisyonlar', function (Blueprint $table) {
                if (
                    Schema::hasColumn('komisyonlar', 'satici_danisman_id') &&
                    ! $this->foreignKeyExists('komisyonlar', 'komisyonlar_satici_danisman_id_foreign')
                ) {
                    $table->foreign('satici_danisman_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null')
                        ->onUpdate('cascade');
                }

                if (
                    Schema::hasColumn('komisyonlar', 'alici_danisman_id') &&
                    ! $this->foreignKeyExists('komisyonlar', 'komisyonlar_alici_danisman_id_foreign')
                ) {
                    $table->foreign('alici_danisman_id')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null')
                        ->onUpdate('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // satislar tablosundan foreign key'leri kaldır
        if (Schema::hasTable('satislar')) {
            Schema::table('satislar', function (Blueprint $table) {
                if ($this->foreignKeyExists('satislar', 'satislar_satici_danisman_id_foreign')) {
                    $table->dropForeign(['satici_danisman_id']);
                }
                if ($this->foreignKeyExists('satislar', 'satislar_alici_danisman_id_foreign')) {
                    $table->dropForeign(['alici_danisman_id']);
                }
            });

            // satislar tablosundan kolonları kaldır
            Schema::table('satislar', function (Blueprint $table) {
                if (Schema::hasColumn('satislar', 'satici_danisman_id')) {
                    $table->dropColumn('satici_danisman_id');
                }
                if (Schema::hasColumn('satislar', 'alici_danisman_id')) {
                    $table->dropColumn('alici_danisman_id');
                }
                if (Schema::hasColumn('satislar', 'satici_komisyon_orani')) {
                    $table->dropColumn('satici_komisyon_orani');
                }
                if (Schema::hasColumn('satislar', 'alici_komisyon_orani')) {
                    $table->dropColumn('alici_komisyon_orani');
                }
                if (Schema::hasColumn('satislar', 'satici_komisyon_tutari')) {
                    $table->dropColumn('satici_komisyon_tutari');
                }
                if (Schema::hasColumn('satislar', 'alici_komisyon_tutari')) {
                    $table->dropColumn('alici_komisyon_tutari');
                }
            });
        }

        // komisyonlar tablosundan foreign key'leri kaldır
        if (Schema::hasTable('komisyonlar')) {
            Schema::table('komisyonlar', function (Blueprint $table) {
                if ($this->foreignKeyExists('komisyonlar', 'komisyonlar_satici_danisman_id_foreign')) {
                    $table->dropForeign(['satici_danisman_id']);
                }
                if ($this->foreignKeyExists('komisyonlar', 'komisyonlar_alici_danisman_id_foreign')) {
                    $table->dropForeign(['alici_danisman_id']);
                }
            });

            // komisyonlar tablosundan kolonları kaldır
            Schema::table('komisyonlar', function (Blueprint $table) {
                if (Schema::hasColumn('komisyonlar', 'satici_danisman_id')) {
                    $table->dropColumn('satici_danisman_id');
                }
                if (Schema::hasColumn('komisyonlar', 'alici_danisman_id')) {
                    $table->dropColumn('alici_danisman_id');
                }
                if (Schema::hasColumn('komisyonlar', 'satici_komisyon_orani')) {
                    $table->dropColumn('satici_komisyon_orani');
                }
                if (Schema::hasColumn('komisyonlar', 'alici_komisyon_orani')) {
                    $table->dropColumn('alici_komisyon_orani');
                }
                if (Schema::hasColumn('komisyonlar', 'satici_komisyon_tutari')) {
                    $table->dropColumn('satici_komisyon_tutari');
                }
                if (Schema::hasColumn('komisyonlar', 'alici_komisyon_tutari')) {
                    $table->dropColumn('alici_komisyon_tutari');
                }
            });
        }
    }

    /**
     * Foreign key'in var olup olmadığını kontrol et
     */
    private function foreignKeyExists(string $table, string $keyName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = DB::select(
            "SELECT COUNT(*) as count
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = ?
             AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$database, $table, $keyName]
        );

        return $result[0]->count > 0;
    }
};
