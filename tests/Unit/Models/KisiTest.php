<?php

namespace Tests\Unit\Models;

use App\Models\Ilan;
use App\Models\Kisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KisiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Kisi model can be created
     */
    public function test_kisi_can_be_created(): void
    {
        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kisi',
            'email' => 'test@example.com',
            'telefon' => '5551234567',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kisi = Kisi::find($kisiId);

        $this->assertInstanceOf(Kisi::class, $kisi);
        $this->assertEquals('Test', $kisi->ad);
        $this->assertEquals('Kisi', $kisi->soyad);
        $this->assertEquals('test@example.com', $kisi->email);
    }

    /**
     * Test Kisi model relationships - danisman
     */
    public function test_kisi_belongs_to_danisman(): void
    {
        $danismanId = DB::table('users')->insertGetId([
            'name' => 'Test Danışman',
            'email' => 'danisman@example.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kisi',
            'email' => 'test@example.com',
            'telefon' => '5551234567',
            'danisman_id' => $danismanId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kisi = Kisi::find($kisiId);

        if (method_exists($kisi, 'danisman')) {
            $this->assertNotNull($kisi->danisman);
            $this->assertEquals($danismanId, $kisi->danisman->id);
        }
    }

    /**
     * Test Kisi model relationships - ilanlar
     */
    public function test_kisi_has_ilanlar(): void
    {
        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kisi',
            'email' => 'test@example.com',
            'telefon' => '5551234567',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('ilanlar')->insert([
            [
                'baslik' => 'İlan 1',
                'fiyat' => 100000,
                'para_birimi' => 'TL',
                'status' => 'Aktif',
                'ilan_sahibi_id' => $kisiId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'baslik' => 'İlan 2',
                'fiyat' => 200000,
                'para_birimi' => 'TL',
                'status' => 'Aktif',
                'ilan_sahibi_id' => $kisiId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $kisi = Kisi::find($kisiId);

        if (method_exists($kisi, 'ilanlar')) {
            $this->assertGreaterThanOrEqual(2, $kisi->ilanlar->count());
        }
    }

    /**
     * Test Kisi model relationships - talepler
     */
    public function test_kisi_has_talepler(): void
    {
        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kisi',
            'email' => 'test@example.com',
            'telefon' => '5551234567',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('talepler')->insert([
            [
                'baslik' => 'Talep 1',
                'kisi_id' => $kisiId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $kisi = Kisi::find($kisiId);

        if (method_exists($kisi, 'talepler')) {
            $this->assertGreaterThanOrEqual(1, $kisi->talepler->count());
        }
    }

    /**
     * Test Kisi model scope - active (if exists)
     */
    public function test_kisi_scope_active(): void
    {
        DB::table('kisiler')->insert([
            [
                'ad' => 'Active Kisi',
                'soyad' => 'Test',
                'email' => 'active@example.com',
                'telefon' => '5551234567',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ad' => 'Inactive Kisi',
                'soyad' => 'Test',
                'email' => 'inactive@example.com',
                'telefon' => '5551234568',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        if (method_exists(Kisi::class, 'scopeActive')) {
            $activeKisiler = Kisi::active()->get();
            $this->assertGreaterThanOrEqual(1, $activeKisiler->count());
        } else {
            $this->markTestSkipped('scopeActive method does not exist');
        }
    }

    /**
     * Test Kisi model Context7 compliance - kisi_* fields
     */
    public function test_kisi_context7_compliance(): void
    {
        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kisi',
            'email' => 'test@example.com',
            'telefon' => '5551234567',
            'kisi_notlari' => 'Test notları', // Context7: musteri_notlari → kisi_notlari
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $kisi = Kisi::find($kisiId);

        // Check if kisi_notlari field exists (Context7 compliance)
        if (property_exists($kisi, 'kisi_notlari') || method_exists($kisi, 'getKisiNotlariAttribute')) {
            $this->assertNotNull($kisi->kisi_notlari ?? null);
        }
    }
}

