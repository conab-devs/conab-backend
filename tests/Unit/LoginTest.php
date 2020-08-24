<?php

namespace Tests\Unit;

use App\User;
use App\Components\Errors\InvalidFieldException;
use App\Components\Errors\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class LoginTest extends TestCase
{
    use MockeryPHPUnitIntegration, RefreshDatabase;

    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new User;
        $this->sut->fill([
            'email' => 'valid@valid.com',
            'password' => 'valid_password',
        ]);
    }

    /** @test */
    public function should_throw_error_if_email_field_is_invalid()
    {
        $this->expectException(InvalidFieldException::class);
        $sut = new User();
        $sut->login('request_password', 'MOBILE');
    }

    /** @test */
    public function should_throw_error_if_password_field_is_invalid()
    {
        $this->expectException(InvalidFieldException::class);
        $this->sut->password = null;
        $this->sut->login('request_password', 'MOBILE');
    }

    /** @test */
    public function should_throw_error_if_credentials_not_match()
    {
        $this->expectException(ValidationException::class);      
        $this->sut->login('invalid_password', 'MOBILE');
    }

    /** @test */
    public function should_throw_error_if_user_not_is_authorized()
    {
        $this->expectException(UnauthorizedException::class);      
        $this->sut->login('valid_password', 'WEB');
    }
}
