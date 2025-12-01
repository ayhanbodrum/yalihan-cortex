<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // satislar
        if (Schema::hasTable('satislar')) {
            // Add columns if missing
            if (! Schema::hasColumn('satislar', 'satici_danisman_id')) {
                Schema::table('satislar', function (Blueprint $table) {
                    $table->unsignedBigInteger('satici_danisman_id')->nullable()->after('danisman_id');
                });
            }
            if (! Schema::hasColumn('satislar', 'alici_danisman_id')) {
                Schema::table('satislar', function (Blueprint $table) {
                    $table->unsignedBigInteger('alici_danisman_id')->nullable()->after('satici_danisman_id');
                });
            }
            if (! Schema::hasColumn('satislar', 'satici_komisyon_orani')) {
                Schema::table('satislar', function (Blueprint $table) {
                    $table->decimal('satici_komisyon_orani', 5, 2)->nullable()->after('komisyon_orani');
                });
            }
            if (! Schema::hasColumn('satislar', 'alici_komisyon_orani')) {
                Schema::table('satislar', function (Blueprint $table) {
                    $table->decimal('alici_komisyon_orani', 5, 2)->nullable()->after('satici_komisyon_orani');
                });
            }
            if (! Schema::hasColumn('satislar', 'satici_komisyon_tutari')) {
                Schema::table('satislar', function (Blueprint $table) {
                    $table->decimal('satici_komisyon_tutari', 15, 2)->nullable()->after('komisyon_tutari');
                });
            }
            if (! Schema::hasColumn('satislar', 'alici_komisyon_tutari')) {
                Schema::table('satislar', function (Blueprint $table) {
                    $table->decimal('alici_komisyon_tutari', 15, 2)->nullable()->after('satici_komisyon_tutari');
                });
            }

            // Foreign keys
            Schema::table('satislar', function (Blueprint $table) {
                if (Schema::hasColumn('satislar', 'satici_danisman_id')) {
                    $table->foreign('satici_danisman_id')->references('id')->on('users')->nullOnDelete();
                }
                if (Schema::hasColumn('satislar', 'alici_danisman_id')) {
                    $table->foreign('alici_danisman_id')->references('id')->on('users')->nullOnDelete();
                }
            });
        }

        // komisyonlar
        if (Schema::hasTable('komisyonlar')) {
            // Add columns if missing
            if (! Schema::hasColumn('komisyonlar', 'satici_danisman_id')) {
                Schema::table('komisyonlar', function (Blueprint $table) {
                    $table->unsignedBigInteger('satici_danisman_id')->nullable()->after('danisman_id');
                });
            }
            if (! Schema::hasColumn('komisyonlar', 'alici_danisman_id')) {
                Schema::table('komisyonlar', function (Blueprint $table) {
                    $table->unsignedBigInteger('alici_danisman_id')->nullable()->after('satici_danisman_id');
                });
            }
            if (! Schema::hasColumn('komisyonlar', 'satici_komisyon_orani')) {
                Schema::table('komisyonlar', function (Blueprint $table) {
                    $table->decimal('satici_komisyon_orani', 5, 2)->nullable()->after('komisyon_orani');
                });
            }
            if (! Schema::hasColumn('komisyonlar', 'alici_komisyon_orani')) {
                Schema::table('komisyonlar', function (Blueprint $table) {
                    $table->decimal('alici_komisyon_orani', 5, 2)->nullable()->after('satici_komisyon_orani');
                });
            }
            if (! Schema::hasColumn('komisyonlar', 'satici_komisyon_tutari')) {
                Schema::table('komisyonlar', function (Blueprint $table) {
                    $table->decimal('satici_komisyon_tutari', 15, 2)->nullable()->after('komisyon_tutari');
                });
            }
            if (! Schema::hasColumn('komisyonlar', 'alici_komisyon_tutari')) {
                Schema::table('komisyonlar', function (Blueprint $table) {
                    $table->decimal('alici_komisyon_tutari', 15, 2)->nullable()->after('satici_komisyon_tutari');
                });
            }

            // Foreign keys
            Schema::table('komisyonlar', function (Blueprint $table) {
                if (Schema::hasColumn('komisyonlar', 'satici_danisman_id')) {
                    $table->foreign('satici_danisman_id')->references('id')->on('users')->nullOnDelete();
                }
                if (Schema::hasColumn('komisyonlar', 'alici_danisman_id')) {
                    $table->foreign('alici_danisman_id')->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // satislar
        if (Schema::hasTable('satislar')) {
            Schema::table('satislar', function (Blueprint $table) {
                // Drop foreign keys first if they exist
                try {
                    $table->dropForeign(['satici_danisman_id']);
                } catch (\Exception $e) {
                }
                try {
                    $table->dropForeign(['alici_danisman_id']);
                } catch (\Exception $e) {
                }

                // Drop columns conditionally
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

        // komisyonlar
        if (Schema::hasTable('komisyonlar')) {
            Schema::table('komisyonlar', function (Blueprint $table) {
                // Drop foreign keys first if they exist
                try {
                    $table->dropForeign(['satici_danisman_id']);
                } catch (\Exception $e) {
                }
                try {
                    $table->dropForeign(['alici_danisman_id']);
                } catch (\Exception $e) {
                }

                // Drop columns conditionally
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
};
