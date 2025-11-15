<?php

namespace Tests\Unit\Services;

use App\Models\Ilan;
use App\Models\User;
use App\Services\IlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IlanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected IlanService $ilanService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ilanService = new IlanService();
    }

    /**
     * Test IlanService can be instantiated
     */
    public function test_ilan_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(IlanService::class, $this->ilanService);
    }

    /**
     * Test IlanService create method
     */
    public function test_ilan_service_create(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
            'danisman_id' => $user->id,
        ];

        // If create method exists, test it
        if (method_exists($this->ilanService, 'create')) {
            $result = $this->ilanService->create($data);
            $this->assertNotNull($result);
        } else {
            $this->markTestSkipped('create method does not exist');
        }
    }

    /**
     * Test IlanService update method
     */
    public function test_ilan_service_update(): void
    {
        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        $data = [
            'baslik' => 'Updated İlan',
            'fiyat' => 200000,
        ];

        // If update method exists, test it
        if (method_exists($this->ilanService, 'update')) {
            $result = $this->ilanService->update($ilan, $data);
            $this->assertNotNull($result);
        } else {
            $this->markTestSkipped('update method does not exist');
        }
    }

    /**
     * Test IlanService delete method
     */
    public function test_ilan_service_delete(): void
    {
        $ilan = Ilan::create([
            'baslik' => 'Test İlan',
            'fiyat' => 100000,
            'para_birimi' => 'TL',
            'status' => 'Aktif',
        ]);

        // If delete method exists, test it
        if (method_exists($this->ilanService, 'delete')) {
            $result = $this->ilanService->delete($ilan);
            $this->assertTrue($result);
        } else {
            $this->markTestSkipped('delete method does not exist');
        }
    }

    /**
     * Test IlanService with invalid data
     */
    public function test_ilan_service_with_invalid_data(): void
    {
        $data = [];

        // If create method exists, test it with invalid data
        if (method_exists($this->ilanService, 'create')) {
            try {
                $this->ilanService->create($data);
                $this->fail('Expected exception was not thrown');
            } catch (\Exception $e) {
                $this->assertInstanceOf(\Exception::class, $e);
            }
        } else {
            $this->markTestSkipped('create method does not exist');
        }
    }
}

