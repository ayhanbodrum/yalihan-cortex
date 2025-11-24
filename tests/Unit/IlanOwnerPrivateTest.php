<?php

namespace Tests\Unit;

use App\Models\Ilan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IlanOwnerPrivateTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_private_data_encrypts_and_reads_back(): void
    {
        $ilan = Ilan::factory()->create([
            'baslik' => 'Test İlan',
            'fiyat' => 1000000,
            'para_birimi' => 'TRY',
        ]);

        $ilan->owner_private_data = [
            'desired_price_min' => 900000,
            'desired_price_max' => 1100000,
            'notes' => 'Ön görüşmede belirlenen aralık'
        ];
        $ilan->save();

        $this->assertNotNull($ilan->owner_private_encrypted);

        $fresh = $ilan->fresh();
        $priv = $fresh->owner_private_data;
        $this->assertSame(900000, $priv['desired_price_min']);
        $this->assertSame(1100000, $priv['desired_price_max']);
        $this->assertSame('Ön görüşmede belirlenen aralık', $priv['notes']);
    }
}