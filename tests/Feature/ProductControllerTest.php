<?php

namespace Tests\Feature;

use App\Category;
use App\Cooperative;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_create_an_product()
    {
        $cooperative = factory(Cooperative::class)->create();
        $admin_coop = factory(User::class)->create([
            'cooperative_id' => $cooperative->id,
            'user_type' => 'ADMIN_COOP'
        ]);
        $category = factory(Category::class)->create();

        $data = [
            'name' => 'any_name',
            'price' => 9.99,
            'photo_path' => UploadedFile::fake()->image('photo.png'),
            'estimated_delivery_time' => 1,
            'category_id' => $category->id
        ];

        $response = $this->actingAs($admin_coop, 'api')
            ->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment($data);
    }
}
