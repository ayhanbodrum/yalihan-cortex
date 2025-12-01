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
     * Context7 Compliance: status instead of durum, English field names
     */
    public function up(): void
    {
        Schema::create('danisman_yorumlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('danisman_id')->constrained('users')->onDelete('cascade'); // Context7: danisman_id
            $table->foreignId('kisi_id')->nullable()->constrained('kisiler')->onDelete('set null'); // Context7: kisi_id, not musteri_id
            $table->string('guest_name', 100)->nullable(); // Misafir yorum yazabilir
            $table->string('guest_email', 255)->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->integer('rating')->default(5)->comment('1-5 arası yıldız'); // Rating 1-5
            $table->text('yorum')->nullable(); // Yorum içeriği
            $table->string('status', 20)->default('pending')->comment('pending, approved, rejected'); // Context7: status
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            $table->text('moderation_reason')->nullable();
            $table->integer('like_count')->default(0);
            $table->integer('dislike_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('danisman_id');
            $table->index('kisi_id');
            $table->index('status');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danisman_yorumlari');
    }
};
