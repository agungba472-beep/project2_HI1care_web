<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_photo_without_pasien_data()
    {
        Storage::fake('public');

        // Create user with role 'pasien' but NO 'pasien' data
        $user = User::create([
            'nama' => 'Test User',
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'pasien',
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->postJson('/api/profile/photo', [
            'photo' => $file,
        ]);

        $response->dump();
        $response->assertStatus(200);
    }
}
