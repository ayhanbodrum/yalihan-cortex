<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('talepler')) {
            return;
        }

        Schema::create('talepler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kisi_id')->constrained('kisiler')->onDelete('cascade');
            $table->foreignId('danisman_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('talep_tipi'); // 'Sat', 'Kirala', 'Al', 'Kirala_Al'
            $table->string('emlak_tipi'); // 'Daire', 'Villa', 'Arsa', vb.
            $table->decimal('min_fiyat', 15, 2)->nullable();
            $table->decimal('max_fiyat', 15, 2)->nullable();
            $table->foreignId('il_id')->nullable()->constrained('iller')->onDelete('set null');
            $table->foreignId('ilce_id')->nullable()->constrained('ilceler')->onDelete('set null');
            $table->text('notlar')->nullable();
            $table->string('status')->default('Aktif'); // Aktif, Pasif, Kapandı
            $table->string('oncelik')->default('Orta'); // Düşük, Orta, Yüksek
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('talep_tipi');
            $table->index('kisi_id');
            $table->index('danisman_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talepler');
    }
};
