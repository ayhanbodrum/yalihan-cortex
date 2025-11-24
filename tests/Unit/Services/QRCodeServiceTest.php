<?php

namespace Tests\Unit\Services;

use App\Models\Ilan;
use App\Models\Setting;
use App\Services\QRCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QRCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QRCodeService $qrCodeService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->qrCodeService = new QRCodeService();
    }

    /**
     * Test QRCodeService can be instantiated
     */
    public function test_qr_code_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(QRCodeService::class, $this->qrCodeService);
    }

    /**
     * Test isEnabled method
     */
    public function test_is_enabled(): void
    {
        // Default should be enabled
        $result = $this->qrCodeService->isEnabled();
        $this->assertIsBool($result);
    }

    /**
     * Test generateForListing method - success
     */
    public function test_generate_for_listing_success(): void
    {
        // Create test ilan
        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        // Enable QR code
        Setting::set('qrcode_enabled', true);

        $result = $this->qrCodeService->generateForListing($ilan->id, [
            'size' => 300,
            'format' => 'svg'
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('base64', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('format', $result);
    }

    /**
     * Test generateForListing method - disabled
     */
    public function test_generate_for_listing_disabled(): void
    {
        // Disable QR code
        Setting::set('qrcode_enabled', false);

        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('QR kod özelliği devre dışı bırakılmış');

        $this->qrCodeService->generateForListing($ilan->id);
    }

    /**
     * Test generateForUrl method
     */
    public function test_generate_for_url(): void
    {
        $url = 'https://example.com/test';
        $result = $this->qrCodeService->generateForUrl($url, [
            'size' => 250,
            'format' => 'svg',
            'filename_prefix' => 'test-qr'
        ]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('base64', $result);
        $this->assertArrayHasKey('filename', $result);
    }

    /**
     * Test generateForWhatsApp method
     */
    public function test_generate_for_whatsapp(): void
    {
        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        $result = $this->qrCodeService->generateForWhatsApp($ilan->id, '+905551234567');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('url', $result);
    }

    /**
     * Test getForListing method
     */
    public function test_get_for_listing(): void
    {
        Setting::set('qrcode_enabled', true);

        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        $result = $this->qrCodeService->getForListing($ilan->id);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('path', $result);
    }

    /**
     * Test deleteForListing method
     */
    public function test_delete_for_listing(): void
    {
        Setting::set('qrcode_enabled', true);

        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        // Generate QR code first
        $this->qrCodeService->generateForListing($ilan->id);

        // Delete it
        $result = $this->qrCodeService->deleteForListing($ilan->id);

        $this->assertTrue($result);
    }

    /**
     * Test getStatistics method
     */
    public function test_get_statistics(): void
    {
        Setting::set('qrcode_enabled', true);

        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        // Generate a QR code
        $this->qrCodeService->generateForListing($ilan->id);

        $stats = $this->qrCodeService->getStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_files', $stats);
        $this->assertArrayHasKey('total_size', $stats);
        $this->assertArrayHasKey('total_size_mb', $stats);
    }
}

