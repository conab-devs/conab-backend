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

//    /** @test */
//    public function should_upload_file()
//    {
//        $user = factory(User::class)->create(['profile_picture' => '', 'user_type' => 'ADMIN_CONAB']);
//        $authenticatedRoute = $this->actingAs($user, 'api');
//
//        Storage::fake('public');
//
//        $authenticatedRoute->postJson('/api/uploads', [
//            'avatar' => UploadedFile::fake()->image('photo.png')
//        ])->assertOk()->assertJsonStructure(['url']);
//
//        Storage::disk('public')->assertExists($user->profile_picture);
//    }
//
//    /** @test */
//    public function on_upload_should_delete_existing_file()
//    {
//        $user = factory(User::class)->create(['profile_picture' => '', 'user_type' => 'ADMIN_CONAB']);
//        $authenticatedRoute = $this->actingAs($user, 'api');
//
//        Storage::fake('public');
//        // upload first file
//        $authenticatedRoute->postJson('/api/uploads', [
//            'avatar' => UploadedFile::fake()->image('photo.png')
//        ])->assertOk();
//        $oldPath = $user->profile_picture;
//        Storage::disk('public')->assertExists($oldPath);
//
//        // upload first second
//        $authenticatedRoute->postJson('/api/uploads', [
//            'avatar' => UploadedFile::fake()->image('photo.png')
//        ])->assertOk();
//        Storage::disk('public')->assertMissing($oldPath);
//    }
//
//    /** @test */
//    public function on_upload_should_throw_an_error_if_no_avatar_is_send()
//    {
//        $user = factory(User::class)->create(['profile_picture' => '', 'user_type' => 'ADMIN_CONAB']);
//        $authenticatedRoute = $this->actingAs($user, 'api');
//
//        Storage::fake('public');
//        $authenticatedRoute->postJson('/api/uploads', ['avatar' => null])
//            ->assertStatus(400)
//            ->assertJson(['error' => 'Avatar is required and should be a valid file']);
//
//        $authenticatedRoute->postJson('/api/uploads', ['avatar' => 'file'])
//            ->assertStatus(400)
//            ->assertJson(['error' => 'Avatar is required and should be a valid file']);
//
//        $authenticatedRoute->postJson('/api/uploads', ['avatar' => 1])
//            ->assertStatus(400)
//            ->assertJson(['error' => 'Avatar is required and should be a valid file']);
//    }
}
