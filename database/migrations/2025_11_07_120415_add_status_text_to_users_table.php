<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Context7 Compliance: status_text kolonu string durumları saklar
     * Boolean status kolonu backward compatibility için kalır
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Status text (String durum: taslak, onay_bekliyor, aktif, satildi, kiralandi, pasif, arsivlendi)
            if (!Schema::hasColumn('users', 'status_text')) {
                $table->string('status_text', 50)->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status_text')) {
                $table->dropColumn('status_text');
            }
        });
    }
};
