<?php

namespace Tests\Unit;

use Tests\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery;
use App\User;
use App\Components\Errors\InvalidArgumentException;
use App\Components\Errors\UnauthorizedException;
use App\Components\AuthHandler;
use App\Components\TokenGenerator;
use App\Components\Services\UserService;

class AuthTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = $this->makeAuthHandler();
    }

    public function makeAuthHandler($userType = 'CUSTOMER', $token = 'valid_jwt_token')
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn('valid@valid.com');
        $user->shouldReceive('getAttribute')->with('password')->andReturn('valid_password');
        $user->shouldReceive('getAttribute')->with('user_type')->andReturn($userType);

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('findByEmail')->with('valid@valid.com')->andReturn($user);

        $tokenGenerator = Mockery::mock(TokenGenerator::class);
        $tokenGenerator->shouldReceive('generateJwt')->andReturn($token);

        return new AuthHandler($userService, $tokenGenerator);
    }

    /** @test */
    public function should_throw_error_if_email_not_passed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sut->authenticate([
            'password' => 'valid_password',
            'device_name'> 'valid_device_name' 
        ]);
    }

    /** @test */
    public function should_throw_error_if_password_not_passed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sut->authenticate([
            'email' => 'valid@valid.com',
            'device_name'> 'valid_device_name' 
        ]);
    }

    /** @test */
    public function should_throw_error_if_device_name_not_passed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->sut->authenticate([
            'email' => 'valid@valid.com',
            'password' => 'valid_password',
        ]);
    }

    /** @test */
    public function should_throw_error_if_client_try_to_access_from_the_web()
    {
        $this->expectException(UnauthorizedException::class);      
        $this->sut->authenticate('valid@valid.com', 'valid_password', 'WEB');
    }

    /** @test */
    public function should_throw_error_if_admin_conab_try_to_access_from_the_mobile()
    {
        $this->expectException(UnauthorizedException::class); 
        $sut = $this->makeAuthHandler('ADMIN_CONAB');   
        $sut->authenticate('valid@valid.com', 'valid_password', 'MOBILE');
    }

    /** @test */
    public function should_throw_error_if_login_fails()
    {
        $this->expectException(UnauthorizedException::class); 

        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn('valid@valid.com');
        $user->shouldReceive('getAttribute')->with('password')->andReturn('valid_password');
        $user->shouldReceive('getAttribute')->with('user_type')->andReturn('CUSTOMER');

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('findByEmail')->with('valid@valid.com')->andReturn($user);

        $tokenGenerator = Mockery::mock(TokenGenerator::class);
        $tokenGenerator->shouldReceive('generateJwt')
            ->with(['email' => 'valid@valid.com', 'password' => 'invalid_password'])
            ->andReturn('');

        $sut = new AuthHandler($userService, $tokenGenerator);

        $sut->authenticate('valid@valid.com', 'invalid_password', 'MOBILE');
    }

    /** @test */
    public function should_return_token_if_authentication_succeed()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('email')->andReturn('valid@valid.com');
        $user->shouldReceive('getAttribute')->with('password')->andReturn('valid_password');
        $user->shouldReceive('getAttribute')->with('user_type')->andReturn('CUSTOMER');

        $userService = Mockery::mock(UserService::class);
        $userService->shouldReceive('findByEmail')->with('valid@valid.com')->andReturn($user);

        $tokenGenerator = Mockery::mock(TokenGenerator::class);
        $tokenGenerator->shouldReceive('generateJwt')
            ->with(['email' => 'valid@valid.com', 'password' => 'valid_password'])
            ->andReturn('valid_token');

        $sut = new AuthHandler($userService, $tokenGenerator);

        $response = $sut->authenticate('valid@valid.com', 'valid_password', 'MOBILE');

        $this->assertEquals('valid_token', $response['token']);
        $this->assertTrue($response['user'] instanceof $user);
    } 
}
