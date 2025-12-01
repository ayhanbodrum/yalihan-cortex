<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    public function test_photo_upload_endpoint_accepts_image()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg', 600, 400);

        $response = $this->post('/api/admin/photos/upload', [
            'photo' => $file,
            'ilan_id' => null,
            'order' => 0,
            'is_featured' => 1,
        ]);

        $response->assertStatus(200);
    }
}
