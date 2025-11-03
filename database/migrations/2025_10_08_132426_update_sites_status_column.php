<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('sites')) {
            return;
        }

        Schema::table('sites', function (Blueprint $table) {
            if (! Schema::hasColumn('sites', 'status')) {
                $table->boolean('status')->default(true)->after('description');
            }
        });

        if (Schema::hasColumn('sites', 'active') && Schema::hasColumn('sites', 'status')) {
            DB::statement('UPDATE sites SET status = active');
        }

        Schema::table('sites', function (Blueprint $table) {
            if (Schema::hasColumn('sites', 'active')) {
                $table->dropColumn('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('sites')) {
            return;
        }

        Schema::table('sites', function (Blueprint $table) {
            if (! Schema::hasColumn('sites', 'active')) {
                $table->boolean('active')->default(true)->after('description');
            }
        });

        if (Schema::hasColumn('sites', 'status') && Schema::hasColumn('sites', 'active')) {
            DB::statement('UPDATE sites SET active = status');
        }

        Schema::table('sites', function (Blueprint $table) {
            if (Schema::hasColumn('sites', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
