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
        Schema::create('ilan_resimleri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ilan_id');
            $table->string('dosya_adi')->comment('Resim dosya adı');
            $table->string('dosya_yolu')->comment('Resim dosya yolu');
            $table->string('dosya_boyutu')->nullable()->comment('Dosya boyutu (bytes)');
            $table->string('mime_type')->nullable()->comment('Dosya MIME tipi');
            $table->integer('sira_no')->default(1)->comment('Resim sıra numarası');
            $table->boolean('ana_resim')->default(false)->comment('Ana resim mi?');
            $table->text('alt_text')->nullable()->comment('Resim alt metni');
            $table->text('aciklama')->nullable()->comment('Resim açıklaması');
            $table->enum('status', ['Aktif', 'Pasif'])->default('Aktif')->comment('Resim durumu');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ilan_id')->references('id')->on('ilanlar')->onDelete('cascade');

            // Indexes
            $table->index('ilan_id', 'idx_ilan_resimleri_ilan_id');
            $table->index(['ilan_id', 'sira_no'], 'idx_ilan_resimleri_ilan_sira');
            $table->index('ana_resim', 'idx_ilan_resimleri_ana_resim');
            $table->index('status', 'idx_ilan_resimleri_status');
            $table->index('created_at', 'idx_ilan_resimleri_created_at');

            // Unique constraint - bir ilan için aynı sıra numarası sadece bir kez olabilir
            $table->unique(['ilan_id', 'sira_no'], 'unique_ilan_resim_sira');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ilan_resimleri');
    }
};
