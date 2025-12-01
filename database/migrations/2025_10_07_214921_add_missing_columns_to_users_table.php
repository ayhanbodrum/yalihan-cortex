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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('users', 'email')) {
                $table->string('email')->unique()->nullable();
            }
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'password')) {
                $table->string('password')->nullable();
            }
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->integer('role_id')->nullable();
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true);
            }
            if (! Schema::hasColumn('users', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable();
            }
            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'name', 'email', 'email_verified_at', 'password', 'role_id', 'status',
                'last_login_at', 'last_activity_at', 'profile_photo_path', 'phone_number',
                'title', 'bio', 'office_address', 'whatsapp_number', 'instagram_profile',
                'linkedin_profile', 'website', 'facebook_profile', 'twitter_profile',
                'telegram_username', 'telegram_chat_id', 'position', 'department',
                'employee_id', 'hire_date', 'termination_date', 'emergency_contact',
                'notes', 'expertise_summary', 'certificates_info', 'remember_token',
                'created_at', 'updated_at', 'deleted_at',
            ]);
        });
    }
};
