<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Context7 Compliance: Eksik danışman profil kolonlarını ekler
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Sosyal medya profilleri (eğer yoksa) - önce bunları ekle
            if (! Schema::hasColumn('users', 'linkedin_profile')) {
                $table->string('linkedin_profile', 255)->nullable()->after('instagram_profile');
            }
            if (! Schema::hasColumn('users', 'facebook_profile')) {
                $table->string('facebook_profile', 255)->nullable()->after('linkedin_profile');
            }
            if (! Schema::hasColumn('users', 'twitter_profile')) {
                $table->string('twitter_profile', 255)->nullable()->after('facebook_profile');
            }
            if (! Schema::hasColumn('users', 'youtube_channel')) {
                $table->string('youtube_channel', 255)->nullable()->after('twitter_profile');
            }
            if (! Schema::hasColumn('users', 'website')) {
                $table->string('website', 255)->nullable()->after('youtube_channel');
            }

            // Lisans numarası (website'den sonra)
            if (! Schema::hasColumn('users', 'lisans_no')) {
                $table->string('lisans_no', 50)->nullable()->after('website');
            }

            // Ofis telefonu
            if (! Schema::hasColumn('users', 'office_phone')) {
                $table->string('office_phone', 20)->nullable()->after('office_address');
            }

            // Deneyim yılı
            if (! Schema::hasColumn('users', 'deneyim_yili')) {
                $table->integer('deneyim_yili')->default(0)->nullable()->after('certificates_info');
            }

            // Status text (String durum: taslak, onay_bekliyor, aktif, satildi, kiralandi, pasif, arsivlendi)
            if (! Schema::hasColumn('users', 'status_text')) {
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
            $columns = [
                'lisans_no',
                'linkedin_profile',
                'facebook_profile',
                'twitter_profile',
                'youtube_channel',
                'website',
                'office_phone',
                'deneyim_yili',
                'status_text',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
