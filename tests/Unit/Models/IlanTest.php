<?php

namespace Tests\Unit\Models;

use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Kisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IlanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Ilan model can be created
     */
    public function test_ilan_can_be_created(): void
    {
        // Create test data using DB::table for simplicity
        $ilanId = DB::table('ilanlar')->insertGetId([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilan = Ilan::find($ilanId);

        $this->assertInstanceOf(Ilan::class, $ilan);
        $this->assertEquals('Test İlan', $ilan->baslik);
        $this->assertEquals(100000, $ilan->fiyat);
        $this->assertEquals('TL', $ilan->para_birimi);
        $this->assertEquals('Aktif', $ilan->status);
    }

    /**
     * Test Ilan model relationships - ilanSahibi
     */
    public function test_ilan_belongs_to_ilan_sahibi(): void
    {
        // Create test data using DB::table
        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kişi',
            'telefon' => '5551234567',
            'email' => 'test@example.com',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilanId = DB::table('ilanlar')->insertGetId([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
            'ilan_sahibi_id' => $kisiId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilan = Ilan::find($ilanId);

        $this->assertInstanceOf(Kisi::class, $ilan->ilanSahibi);
        $this->assertEquals($kisiId, $ilan->ilanSahibi->id);
    }

    /**
     * Test Ilan model relationships - danisman
     */
    public function test_ilan_belongs_to_danisman(): void
    {
        // Create test data using DB::table
        $danismanId = DB::table('users')->insertGetId([
            'name' => 'Test Danışman',
            'email' => 'danisman@example.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilanId = DB::table('ilanlar')->insertGetId([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
            'danisman_id' => $danismanId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilan = Ilan::find($ilanId);

        $this->assertInstanceOf(User::class, $ilan->danisman);
        $this->assertEquals($danismanId, $ilan->danisman->id);
    }

    /**
     * Test Ilan model relationships - kategori
     */
    public function test_ilan_belongs_to_kategori(): void
    {
        // Create test data using DB::table
        $kategoriId = DB::table('ilan_kategorileri')->insertGetId([
            'name' => 'Test Kategori',
            'slug' => 'test-kategori',
            'status' => 'Aktif',
            'display_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilanId = DB::table('ilanlar')->insertGetId([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
            'alt_kategori_id' => $kategoriId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilan = Ilan::find($ilanId);

        $this->assertInstanceOf(IlanKategori::class, $ilan->kategori);
        $this->assertEquals($kategoriId, $ilan->kategori->id);
    }

    /**
     * Test Ilan model scope - active
     */
    public function test_ilan_scope_active(): void
    {
        // Create test data
        DB::table('ilanlar')->insert([
            ['baslik' => 'Aktif İlan', 'fiyat' => 100000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['baslik' => 'Pasif İlan', 'fiyat' => 200000, 'para_birimi' => 'TL', 'status' => 'Pasif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $activeIlans = Ilan::active()->get();

        $this->assertGreaterThanOrEqual(1, $activeIlans->count());
        $this->assertTrue($activeIlans->every(fn($ilan) => $ilan->status === 'Aktif'));
    }

    /**
     * Test Ilan model scope - pending
     */
    public function test_ilan_scope_pending(): void
    {
        // Create test data
        DB::table('ilanlar')->insert([
            ['baslik' => 'Beklemede İlan', 'fiyat' => 100000, 'para_birimi' => 'TL', 'status' => 'Beklemede', 'created_at' => now(), 'updated_at' => now()],
            ['baslik' => 'Aktif İlan', 'fiyat' => 200000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $pendingIlans = Ilan::pending()->get();

        $this->assertGreaterThanOrEqual(1, $pendingIlans->count());
        $this->assertTrue($pendingIlans->every(fn($ilan) => $ilan->status === 'Beklemede'));
    }

    /**
     * Test Ilan model Filterable trait - priceRange
     */
    public function test_ilan_price_range_filter(): void
    {
        // Create test data
        DB::table('ilanlar')->insert([
            ['baslik' => 'İlan 1', 'fiyat' => 100000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['baslik' => 'İlan 2', 'fiyat' => 200000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['baslik' => 'İlan 3', 'fiyat' => 300000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $results = Ilan::query()
            ->priceRange(150000, 250000, 'fiyat')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
        $this->assertTrue($results->every(fn($ilan) => $ilan->fiyat >= 150000 && $ilan->fiyat <= 250000));
    }

    /**
     * Test Ilan model Filterable trait - search
     */
    public function test_ilan_search_filter(): void
    {
        // Create test data
        DB::table('ilanlar')->insert([
            ['baslik' => 'Lüks Villa', 'fiyat' => 100000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['baslik' => 'Modern Daire', 'fiyat' => 200000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $results = Ilan::query()
            ->search('Villa')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
        $this->assertTrue($results->contains(fn($ilan) => str_contains($ilan->baslik, 'Villa')));
    }

    /**
     * Test Ilan model Filterable trait - byStatus
     */
    public function test_ilan_status_filter(): void
    {
        // Create test data
        DB::table('ilanlar')->insert([
            ['baslik' => 'Aktif İlan', 'fiyat' => 100000, 'para_birimi' => 'TL', 'status' => 'Aktif', 'created_at' => now(), 'updated_at' => now()],
            ['baslik' => 'Pasif İlan', 'fiyat' => 200000, 'para_birimi' => 'TL', 'status' => 'Pasif', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $results = Ilan::query()
            ->byStatus('Aktif')
            ->get();

        $this->assertGreaterThanOrEqual(1, $results->count());
        $this->assertTrue($results->every(fn($ilan) => $ilan->status === 'Aktif'));
    }

    /**
     * Test Ilan model SoftDeletes trait
     */
    public function test_ilan_soft_deletes(): void
    {
        $ilanId = DB::table('ilanlar')->insertGetId([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $ilan = Ilan::find($ilanId);
        $ilan->delete();

        $this->assertSoftDeleted('ilanlar', ['id' => $ilanId]);
        $this->assertNull(Ilan::find($ilanId));
        $this->assertNotNull(Ilan::withTrashed()->find($ilanId));
    }
}

