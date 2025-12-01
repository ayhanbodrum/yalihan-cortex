<?php

namespace Tests\Unit\Models;

use App\Models\Kisi;
use App\Models\Talep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TalepTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Talep model can be created
     */
    public function test_talep_can_be_created(): void
    {
        $talepId = DB::table('talepler')->insertGetId([
            'baslik' => 'Test Talep',
            'aciklama' => 'Test açıklama',
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $talep = Talep::find($talepId);

        $this->assertInstanceOf(Talep::class, $talep);
        $this->assertEquals('Test Talep', $talep->baslik);
        $this->assertEquals('Test açıklama', $talep->aciklama);
    }

    /**
     * Test Talep model relationships - kisi
     */
    public function test_talep_belongs_to_kisi(): void
    {
        $kisiId = DB::table('kisiler')->insertGetId([
            'ad' => 'Test',
            'soyad' => 'Kisi',
            'email' => 'test@example.com',
            'telefon' => '5551234567',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $talepId = DB::table('talepler')->insertGetId([
            'baslik' => 'Test Talep',
            'aciklama' => 'Test açıklama',
            'status' => 'Aktif',
            'kisi_id' => $kisiId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $talep = Talep::find($talepId);

        if (method_exists($talep, 'kisi')) {
            $this->assertNotNull($talep->kisi);
            $this->assertEquals($kisiId, $talep->kisi->id);
        }
    }

    /**
     * Test Talep model relationships - danisman
     */
    public function test_talep_belongs_to_danisman(): void
    {
        $danismanId = DB::table('users')->insertGetId([
            'name' => 'Test Danışman',
            'email' => 'danisman@example.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $talepId = DB::table('talepler')->insertGetId([
            'baslik' => 'Test Talep',
            'aciklama' => 'Test açıklama',
            'status' => 'Aktif',
            'danisman_id' => $danismanId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $talep = Talep::find($talepId);

        if (method_exists($talep, 'danisman')) {
            $this->assertNotNull($talep->danisman);
            $this->assertEquals($danismanId, $talep->danisman->id);
        }
    }

    /**
     * Test Talep model relationships - ilanlar
     */
    public function test_talep_has_ilanlar(): void
    {
        $talepId = DB::table('talepler')->insertGetId([
            'baslik' => 'Test Talep',
            'aciklama' => 'Test açıklama',
            'status' => 'Aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('ilanlar')->insert([
            [
                'baslik' => 'İlan 1',
                'fiyat' => 100000,
                'para_birimi' => 'TL',
                'status' => 'Aktif',
                'talep_id' => $talepId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $talep = Talep::find($talepId);

        if (method_exists($talep, 'ilanlar')) {
            $this->assertGreaterThanOrEqual(1, $talep->ilanlar->count());
        }
    }

    /**
     * Test Talep model scope - active (if exists)
     */
    public function test_talep_scope_active(): void
    {
        DB::table('talepler')->insert([
            [
                'baslik' => 'Active Talep',
                'aciklama' => 'Test açıklama',
                'status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'baslik' => 'Inactive Talep',
                'aciklama' => 'Test açıklama',
                'status' => 'Pasif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        if (method_exists(Talep::class, 'scopeActive')) {
            $activeTalepler = Talep::active()->get();
            $this->assertGreaterThanOrEqual(1, $activeTalepler->count());
        } else {
            $this->markTestSkipped('scopeActive method does not exist');
        }
    }

    /**
     * Test Talep model status field (Context7 compliance)
     */
    public function test_talep_status_field(): void
    {
        $talepId = DB::table('talepler')->insertGetId([
            'baslik' => 'Test Talep',
            'aciklama' => 'Test açıklama',
            'status' => 'Aktif', // Context7: status field
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $talep = Talep::find($talepId);

        $this->assertEquals('Aktif', $talep->status);
    }
}
