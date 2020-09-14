<?php

namespace Tests\Feature;

use App\PasswordReset;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** @auhor Franklyn */
class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private $credentials;
    private $user;

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
        $this->user = factory(User::class)->create($this->credentials);

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
        $this->makeUser();

        $token = auth()->attempt($this->credentials);
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/conab/admins');
        
 
        $response->assertStatus(200);
    }

    /** @test */
    public function should_update_the_user_password_if_reset_token_is_valid()
    {
        $this->makeUser();

        $requestResponse = $this->postJson(
            '/api/password/reset/request',
            ['email' => $this->user->email]
        );

        $requestResponse->assertStatus(200);

        $token = (PasswordReset::where('email', $this->user->email)->first())->token;

        $resetResponse = $this->postJson(
            '/api/password/reset',
            [
                'email' => $this->user->email,
                'password' => 'new_password',
                'token' => $token,
            ]
        );
        $resetResponse->assertStatus(200);

        $this->user->refresh();
        
        $this->assertTrue(Hash::check('new_password', $this->user->password));
    }
}
