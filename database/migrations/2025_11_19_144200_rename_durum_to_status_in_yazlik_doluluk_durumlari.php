<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('yazlik_doluluk_durumlari')) {
            return;
        }

        Schema::table('yazlik_doluluk_durumlari', function (Blueprint $table) {
            if (! Schema::hasColumn('yazlik_doluluk_durumlari', 'status')) {
                $table->enum('status', ['musait', 'rezerve', 'bloke', 'bakim', 'temizlik', 'kapali'])->default('musait')->after('durum');
            }
        });

        if (Schema::hasColumn('yazlik_doluluk_durumlari', 'durum') && Schema::hasColumn('yazlik_doluluk_durumlari', 'status')) {
            DB::statement("UPDATE yazlik_doluluk_durumlari SET status = durum WHERE status IS NULL OR status = ''");
        }

        Schema::table('yazlik_doluluk_durumlari', function (Blueprint $table) {
            if (Schema::hasColumn('yazlik_doluluk_durumlari', 'durum')) {
                try {
                    $table->dropIndex('yazlik_doluluk_durumlari_ilan_id_durum_index');
                } catch (\Exception $e) {
                }
                try {
                    $table->dropIndex('yazlik_doluluk_durumlari_ilan_id_tarih_durum_index');
                } catch (\Exception $e) {
                }
                try {
                    $table->dropIndex('yazlik_doluluk_durumlari_tarih_durum_index');
                } catch (\Exception $e) {
                }
                $table->dropColumn('durum');
            }
        });

        Schema::table('yazlik_doluluk_durumlari', function (Blueprint $table) {
            if (Schema::hasColumn('yazlik_doluluk_durumlari', 'status')) {
                try {
                    $table->index(['ilan_id', 'status'], 'yazlik_doluluk_durumlari_ilan_id_status_index');
                } catch (\Exception $e) {
                }
                try {
                    $table->index(['ilan_id', 'tarih', 'status'], 'yazlik_doluluk_durumlari_ilan_id_tarih_status_index');
                } catch (\Exception $e) {
                }
                try {
                    $table->index(['tarih', 'status'], 'yazlik_doluluk_durumlari_tarih_status_index');
                } catch (\Exception $e) {
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('yazlik_doluluk_durumlari')) {
            return;
        }

        Schema::table('yazlik_doluluk_durumlari', function (Blueprint $table) {
            if (! Schema::hasColumn('yazlik_doluluk_durumlari', 'durum')) {
                $table->enum('durum', ['musait', 'rezerve', 'bloke', 'bakim', 'temizlik', 'kapali'])->default('musait')->after('status');
            }
        });

        if (Schema::hasColumn('yazlik_doluluk_durumlari', 'durum') && Schema::hasColumn('yazlik_doluluk_durumlari', 'status')) {
            DB::statement("UPDATE yazlik_doluluk_durumlari SET durum = status WHERE durum IS NULL OR durum = ''");
        }

        Schema::table('yazlik_doluluk_durumlari', function (Blueprint $table) {
            try {
                $table->dropIndex('yazlik_doluluk_durumlari_ilan_id_status_index');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('yazlik_doluluk_durumlari_ilan_id_tarih_status_index');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('yazlik_doluluk_durumlari_tarih_status_index');
            } catch (\Exception $e) {
            }
            if (Schema::hasColumn('yazlik_doluluk_durumlari', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('yazlik_doluluk_durumlari', function (Blueprint $table) {
            try {
                $table->index(['ilan_id', 'durum'], 'yazlik_doluluk_durumlari_ilan_id_durum_index');
            } catch (\Exception $e) {
            }
            try {
                $table->index(['ilan_id', 'tarih', 'durum'], 'yazlik_doluluk_durumlari_ilan_id_tarih_durum_index');
            } catch (\Exception $e) {
            }
            try {
                $table->index(['tarih', 'durum'], 'yazlik_doluluk_durumlari_tarih_durum_index');
            } catch (\Exception $e) {
            }
        });
    }
};
