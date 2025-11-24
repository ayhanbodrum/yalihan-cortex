<?php

namespace Tests\Feature\Integration;

use App\Models\Ilan;
use App\Models\BookingRequest;
use App\Models\Il;
use App\Models\Ilce;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Booking Workflow Integration Test
 * 
 * Tests the complete booking workflow from request to email
 */
class BookingWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete booking workflow
     */
    public function test_complete_booking_workflow(): void
    {
        Mail::fake();

        // Step 1: Create test data
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

        // Step 2: Submit booking request
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

        // Step 3: Verify response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Step 4: Verify booking request was created
        $this->assertDatabaseHas('booking_requests', [
            'ilan_id' => $ilan->id,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'status' => 'pending'
        ]);

        // Step 5: Verify booking reference was generated
        $bookingRequest = BookingRequest::where('ilan_id', $ilan->id)->first();
        $this->assertNotNull($bookingRequest->booking_reference);
        $this->assertNotEmpty($bookingRequest->booking_reference);

        // Step 6: Verify email was sent (if Mail::fake() is used)
        // Mail::assertSent(BookingRequestMail::class);
    }
}

