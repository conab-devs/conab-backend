<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_upload_file()
    {
        $user = factory(User::class)->create(['profile_picture' => '', 'user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        Storage::fake('public');

        $file = UploadedFile::fake()->image('photo.png');

        $authenticatedRoute->postJson('/api/uploads', [
            'avatar' => $file,
        ])->assertOk()->assertJsonStructure(['url']);

        Storage::disk('public')->assertExists('uploads/' . $file->hashName());
    }

    /** @test */
    public function on_upload_should_throw_an_error_if_no_avatar_is_send()
    {
        $user = factory(User::class)->create(['profile_picture' => '', 'user_type' => 'ADMIN_CONAB']);
        $authenticatedRoute = $this->actingAs($user, 'api');

        Storage::fake('public');
        $authenticatedRoute->postJson('/api/uploads', ['avatar' => null])
            ->assertStatus(400)
            ->assertJson(['error' => 'Avatar is required and should be a valid file']);

        $authenticatedRoute->postJson('/api/uploads', ['avatar' => 'file'])
            ->assertStatus(400)
            ->assertJson(['error' => 'Avatar is required and should be a valid file']);

        $authenticatedRoute->postJson('/api/uploads', ['avatar' => 1])
            ->assertStatus(400)
            ->assertJson(['error' => 'Avatar is required and should be a valid file']);
    }
}
