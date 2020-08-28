<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private $credentials;

    public function setUp(): void
    {
        parent::setUp();

        $this->credentials = [
            'email' => $this->faker()->unique()->safeEmail,
            'password' => 'valid_password',
        ];
    }

    public function makeUser($device_name = null): void
    {
        factory(User::class)->create($this->credentials);

        if ($device_name !== null) {
            $this->credentials['device_name'] = $device_name;
        }
    }

    /** @test */
    public function should_make_login_and_return_token()
    {
        $this->makeUser('MOBILE');

        $response = $this->postJson('/api/login', $this->credentials);

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response);
        $this->assertArrayHasKey('user', $response);
    }

    /** @test */
    public function should_try_make_login_and_return_Unauthorized()
    {
        $this->credentials['device_name'] = 'MOBILE';
        $response = $this->postJson('/api/login', $this->credentials);

        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_unauthorized()
    {
        $this->makeUser('WEB');

        $response = $this->postJson('/api/login', $this->credentials);

        $response->assertStatus(401);
        $this->assertEquals($response['message'], "You don't have authorization to this resource");
    }

    /** @test */
    public function should_make_login_and_access_get_route_with_success()
    {
        $this->makeUser('MOBILE');
        array_pop($this->credentials);
        $token = auth()->attempt($this->credentials);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/hello');

        $response->assertStatus(200);
    }

    /** @test */
    public function should_make_logout()
    {
        $this->makeUser();
        $user = User::first();
        $token = \JWTAuth::fromUser($user);

        $this->post('api/logout?token=' . $token)
            ->assertStatus(200);

        $this->assertGuest('api');

    }
}
