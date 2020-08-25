<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\User;

class LoginTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function should_make_login_and_return_token()
    {
        $faker = $this->faker();

        $userCredentials = [
            'email' => $faker->unique()->safeEmail,
            'password' => 'valid_password'
        ];
        
        factory(User::class)->create($userCredentials);

        $userCredentials['device_name'] = 'MOBILE';

        $response = $this->postJson('/api/login', $userCredentials);
        
        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response);
    }

    /** @test */
    public function should_return_unauthorized()
    {
        $faker = $this->faker();

        $userCredentials = [
            'email' => $faker->unique()->safeEmail,
            'password' => 'valid_password'
        ];
        
        factory(User::class)->create($userCredentials);

        $userCredentials['device_name'] = 'WEB';

        $response = $this->postJson('/api/login', $userCredentials);
        
        $response->assertStatus(401);
        $this->assertEquals($response['message'], "You don't have authorization to this resource");
    }
}
