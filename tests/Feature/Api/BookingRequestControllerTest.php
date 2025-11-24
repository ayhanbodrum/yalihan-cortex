<?php

namespace Tests\Feature\Api;

use App\Models\Ilan;
use App\Models\BookingRequest;
use App\Models\User;
use App\Models\Il;
use App\Models\Ilce;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test store booking request - success
     */
    public function test_store_booking_request_success(): void
    {
        Mail::fake();

        // Create test data
        $il = Il::create(['il_adi' => 'İstanbul', 'plaka' => '34']);
        $ilce = Ilce::create(['ilce_adi' => 'Kadıköy', 'il_id' => $il->id]);
        
        $ilan = Ilan::create([
            'baslik' => 'Test Villa',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'il_id' => $il->id,
            'ilce_id' => $ilce->id,
            'status' => 'Aktif',
        ]);

        $response = $this->postJson('/api/booking-request', [
            'villa_id' => $ilan->id,
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(10)->format('Y-m-d'),
            'guests' => 4,
            'name' => 'Test Guest',
            'phone' => '+905551234567',
            'email' => 'guest@example.com',
            'message' => 'Test message',
            'nights' => 3,
            'total_price' => 3000
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'booking_reference',
                    'villa_id',
                    'check_in',
                    'check_out'
                ]
            ]);

        // Verify booking request was created
        $this->assertDatabaseHas('booking_requests', [
            'ilan_id' => $ilan->id,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'status' => 'pending'
        ]);
    }

    /**
     * Test store booking request - validation error
     */
    public function test_store_booking_request_validation_error(): void
    {
        $response = $this->postJson('/api/booking-request', [
            'villa_id' => 999, // Non-existent villa
            'check_in' => now()->subDays(1)->format('Y-m-d'), // Past date
            'check_out' => now()->addDays(1)->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ]);
    }

    /**
     * Test store booking request - check_out before check_in
     */
    public function test_store_booking_request_checkout_before_checkin(): void
    {
        $il = Il::create(['il_adi' => 'İstanbul', 'plaka' => '34']);
        $ilce = Ilce::create(['ilce_adi' => 'Kadıköy', 'il_id' => $il->id]);
        
        $ilan = Ilan::create([
            'baslik' => 'Test Villa',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'il_id' => $il->id,
            'ilce_id' => $ilce->id,
            'status' => 'Aktif',
        ]);

        $response = $this->postJson('/api/booking-request', [
            'villa_id' => $ilan->id,
            'check_in' => now()->addDays(10)->format('Y-m-d'),
            'check_out' => now()->addDays(7)->format('Y-m-d'), // Before check_in
            'guests' => 4,
            'name' => 'Test Guest',
            'phone' => '+905551234567',
            'email' => 'guest@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test store booking request - invalid guests count
     */
    public function test_store_booking_request_invalid_guests(): void
    {
        $il = Il::create(['il_adi' => 'İstanbul', 'plaka' => '34']);
        $ilce = Ilce::create(['ilce_adi' => 'Kadıköy', 'il_id' => $il->id]);
        
        $ilan = Ilan::create([
            'baslik' => 'Test Villa',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'il_id' => $il->id,
            'ilce_id' => $ilce->id,
            'status' => 'Aktif',
        ]);

        $response = $this->postJson('/api/booking-request', [
            'villa_id' => $ilan->id,
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(10)->format('Y-m-d'),
            'guests' => 100, // Exceeds max
            'name' => 'Test Guest',
            'phone' => '+905551234567',
            'email' => 'guest@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test store booking request - invalid email
     */
    public function test_store_booking_request_invalid_email(): void
    {
        $il = Il::create(['il_adi' => 'İstanbul', 'plaka' => '34']);
        $ilce = Ilce::create(['ilce_adi' => 'Kadıköy', 'il_id' => $il->id]);
        
        $ilan = Ilan::create([
            'baslik' => 'Test Villa',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'il_id' => $il->id,
            'ilce_id' => $ilce->id,
            'status' => 'Aktif',
        ]);

        $response = $this->postJson('/api/booking-request', [
            'villa_id' => $ilan->id,
            'check_in' => now()->addDays(7)->format('Y-m-d'),
            'check_out' => now()->addDays(10)->format('Y-m-d'),
            'guests' => 4,
            'name' => 'Test Guest',
            'phone' => '+905551234567',
            'email' => 'invalid-email', // Invalid email
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }
}

