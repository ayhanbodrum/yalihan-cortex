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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
            $table->string('booking_reference')->unique();

            // Misafir bilgileri
            $table->string('guest_name');
            $table->string('guest_phone');
            $table->string('guest_email');
            $table->text('guest_message')->nullable();

            // Rezervasyon bilgileri
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guests')->default(1);
            $table->integer('nights')->default(1);
            $table->decimal('total_price', 10, 2)->default(0);

            // Villa bilgileri (cache)
            $table->string('villa_title')->nullable();
            $table->string('villa_location')->nullable();

            // Durum
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('ilan_id');
            $table->index('status');
            $table->index('booking_reference');
            $table->index('guest_email');
            $table->index('check_in');
            $table->index('check_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
