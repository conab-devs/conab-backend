<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function makeUser(): array
    {
        $userCredentials = [
            'email' =>  $this->faker()->unique()->safeEmail,
            'password' => 'valid_password',
        ];

        factory(User::class)->create($userCredentials);

        $userCredentials['device_name'] = 'MOBILE';
        
        return $userCredentials;
    }

    /** @test */
    public function should_make_login_and_return_token()
    {
        $response = $this->postJson('/api/login', $this->makeUser());

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response);
    }

    /** @test */
    public function should_try_make_login_and_return_Unauthorized()
    {
        $userCredentials = [
            'email' => 'unexistent@mail.com',
            'password' => 'valid_password',
        ];

        $userCredentials['device_name'] = 'MOBILE';

        $response = $this->postJson('/api/login', $userCredentials);

        $response->assertStatus(401);
    }

    /** @test */
    public function should_return_unauthorized()
    {
        $userCredentials = $this->makeUser();

        $userCredentials['device_name'] = 'WEB';

        $response = $this->postJson('/api/login', $userCredentials);

        $response->assertStatus(401);
        $this->assertEquals($response['message'], "You don't have authorization to this resource");
    }

    /** @test */
    public function should_make_login_and_access_get_route_with_success()
    {
        $credentials = $this->makeUser();
        array_pop($credentials);
        $token = auth()->attempt($credentials);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/hello');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function should_make_logout()
    {
        $credentials = $this->makeUser();
        array_pop($credentials);
        $token = auth()->attempt($credentials);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson('/api/logout');

        $response->assertStatus(200);
    }
}
