<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     * Context7 Compliance: All fields in English, status instead of aktif
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Basic profile fields
            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 20)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'title')) {
                $table->string('title', 100)->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('title');
            }
            if (! Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path', 500)->nullable()->after('bio');
            }

            // Office/Contact information
            if (! Schema::hasColumn('users', 'office_address')) {
                $table->text('office_address')->nullable()->after('phone_number');
            }
            if (! Schema::hasColumn('users', 'office_phone')) {
                $table->string('office_phone', 20)->nullable()->after('office_address');
            }

            // Social media links (Context7: English field names)
            if (! Schema::hasColumn('users', 'instagram_profile')) {
                $table->string('instagram_profile', 255)->nullable()->after('office_phone');
            }
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
            if (! Schema::hasColumn('users', 'tiktok_profile')) {
                $table->string('tiktok_profile', 255)->nullable()->after('youtube_channel');
            }
            if (! Schema::hasColumn('users', 'whatsapp_number')) {
                $table->string('whatsapp_number', 20)->nullable()->after('tiktok_profile');
            }
            if (! Schema::hasColumn('users', 'telegram_username')) {
                $table->string('telegram_username', 100)->nullable()->after('whatsapp_number');
            }
            if (! Schema::hasColumn('users', 'website')) {
                $table->string('website', 255)->nullable()->after('telegram_username');
            }

            // Professional information
            if (! Schema::hasColumn('users', 'lisans_no')) {
                $table->string('lisans_no', 50)->nullable()->after('website');
            }
            if (! Schema::hasColumn('users', 'uzmanlik_alani')) {
                $table->string('uzmanlik_alani', 255)->nullable()->after('lisans_no');
            }
            if (! Schema::hasColumn('users', 'expertise_summary')) {
                $table->text('expertise_summary')->nullable()->after('uzmanlik_alani');
            }
            if (! Schema::hasColumn('users', 'certificates_info')) {
                $table->text('certificates_info')->nullable()->after('expertise_summary');
            }
            if (! Schema::hasColumn('users', 'deneyim_yili')) {
                $table->integer('deneyim_yili')->default(0)->after('certificates_info');
            }

            // Verification and status
            if (! Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('status');
            }

            // JSON fields for arrays
            if (! Schema::hasColumn('users', 'uzmanlik_alanlari')) {
                $table->json('uzmanlik_alanlari')->nullable()->after('deneyim_yili');
            }
            if (! Schema::hasColumn('users', 'bolge_uzmanliklari')) {
                $table->json('bolge_uzmanliklari')->nullable()->after('uzmanlik_alanlari');
            }
            if (! Schema::hasColumn('users', 'diller')) {
                $table->json('diller')->nullable()->after('bolge_uzmanliklari');
            }
            if (! Schema::hasColumn('users', 'calisma_saatleri')) {
                $table->json('calisma_saatleri')->nullable()->after('diller');
            }
            if (! Schema::hasColumn('users', 'iletisim_tercihleri')) {
                $table->json('iletisim_tercihleri')->nullable()->after('calisma_saatleri');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number', 'title', 'bio', 'profile_photo_path',
                'office_address', 'office_phone',
                'instagram_profile', 'linkedin_profile', 'facebook_profile',
                'twitter_profile', 'youtube_channel', 'tiktok_profile',
                'whatsapp_number', 'telegram_username', 'website',
                'lisans_no', 'uzmanlik_alani', 'expertise_summary',
                'certificates_info', 'deneyim_yili', 'is_verified',
                'uzmanlik_alanlari', 'bolge_uzmanliklari', 'diller',
                'calisma_saatleri', 'iletisim_tercihleri',
            ]);
        });
    }
};
