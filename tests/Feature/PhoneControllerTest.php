<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function should_return_validation_error_if_no_phone_is_passed()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->postJson('api/phones', []);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_phones()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->postJson('api/phones', [
            'phones' => 123,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_wrong_format_phones_are_passed()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->postJson('api/phones', [
            'phones' => [
                ['number' => '85 85858-8585'],
                ['number' => '85 86428-1575'],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_duplicated_phones_are_passed()
    {
        $user = factory(\App\User::class)->create();
        $user->phones()->create([
            'number' => '(11) 11111-1111',
        ]);

        $response = $this->actingAs($user)->postJson('api/phones', [
            'phones' => [
                ['number' => '(11) 11111-1111'],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_store_a_phone()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->postJson('api/phones', [
            'phones' => [
                ['number' => '(12) 12122-1212'],
            ],
        ]);

        $response->assertCreated();

        $response->assertJsonFragment(['number' => '(12) 12122-1212']);

        $this->assertDatabaseHas('phones', [
            'number' => '(12) 12122-1212',
        ]);
    }

    /** @test */
    public function should_return_validation_error_if_array_is_not_passed_to_phones_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/phones", [
            'phones' => 123,
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_wrong_format_phones_are_passed_on_update()
    {
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user)->putJson("api/phones", [
            'phones' => [
                ['number' => '85 85858-8585'],
                ['number' => '85 86428-1575'],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_return_validation_error_if_duplicated_phones_are_passed_on_update()
    {
        $user = factory(\App\User::class)->create();
        $user->phones()->create([
            'number' => '(11) 11111-1111',
        ]);

        $response = $this->actingAs($user)->putJson("api/phones", [
            'phones' => [
                ['number' => '(11) 11111-1111'],
            ],
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function should_update_user_phones()
    {
        $user = factory(\App\User::class)->create();

        $user->phones()
            ->create(factory(\App\Phone::class)
                    ->make()
                    ->toArray());

        $new_phone = [
            'phones' => [
                [
                    'number' => '(11) 11111-1111)',
                ],
            ],
        ];

        $response = $this->actingAs($user)
            ->putJson('api/phones', $new_phone);
        $response->assertStatus(200);

        $response->assertJson($new_phone['phones']);
    }

    /** @test */
    public function should_get_user_phones()
    {
        $user = factory(\App\User::class)->create();

        $phone = $user->phones()
            ->create(factory(\App\Phone::class)
                    ->make()
                    ->toArray());

        $response = $this->actingAs($user)->getJson('api/phones');

        $response->assertJsonFragment($phone->toArray());
    }
}
