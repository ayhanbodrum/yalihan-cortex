<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (! Schema::hasColumn('ilanlar', 'video_url')) {
                $table->string('video_url')->nullable()->after('youtube_video_url');
            }

            if (! Schema::hasColumn('ilanlar', 'video_status')) {
                $table->string('video_status')->default('none')->after('video_url');
            }

            if (! Schema::hasColumn('ilanlar', 'video_last_frame')) {
                $table->unsignedTinyInteger('video_last_frame')->nullable()->after('video_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ilanlar', function (Blueprint $table) {
            if (Schema::hasColumn('ilanlar', 'video_url')) {
                $table->dropColumn('video_url');
            }

            if (Schema::hasColumn('ilanlar', 'video_status')) {
                $table->dropColumn('video_status');
            }

            if (Schema::hasColumn('ilanlar', 'video_last_frame')) {
                $table->dropColumn('video_last_frame');
            }
        });
    }
};
