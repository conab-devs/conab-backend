<?php

namespace Tests\Unit;

use App\User;
use App\Components\Errors\InvalidFieldException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class LoginTest extends TestCase
{
    use MockeryPHPUnitIntegration, RefreshDatabase;

    /** @test */
    public function should_throw_error_if_email_field_is_invalid()
    {
        $this->expectException(InvalidFieldException::class);
        
        $sut = new User();

        $sut->login('request_password');
    }

    /** @test */
    public function should_throw_error_if_password_field_is_invalid()
    {
        $this->expectException(InvalidFieldException::class);
        
        $sut = new User();
        $sut->email = 'valid@valid.com';

        $sut->login('request_password');
    }

    /** @test */
    public function should_throw_error_if_credentials_not_match()
    {
        $this->expectException(ValidationException::class);
        
        $sut = new User();
        $sut->email = 'valid@valid.com';
        $sut->password = 'valid_password';

        $sut->login('invalid_password');
    }
}
