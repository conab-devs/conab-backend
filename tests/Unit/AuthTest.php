<?php

namespace Tests\Unit;

use App\User;
use App\Components\Errors\InvalidFieldException;
use App\Components\Errors\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery;

class AuthTest extends TestCase
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
            'user_type' => 'CUSTOMER'
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
    public function should_throw_error_if_client_try_to_access_from_the_web()
    {
        $this->expectException(UnauthorizedException::class);      
        $this->sut->login('valid_password', 'WEB');
    }

    /** @test */
    public function should_throw_error_if_admin_conab_try_to_access_from_the_mobile()
    {
        $this->expectException(UnauthorizedException::class); 
        $this->sut->user_type = 'ADMIN_CONAB';     
        $this->sut->login('valid_password', 'MOBILE');
    }

    /** @test */
    public function should_throw_error_if_super_admin_try_to_access_from_the_mobile()
    {
        $this->expectException(UnauthorizedException::class); 
        $this->sut->user_type = 'SUPER_ADMIN';     
        $this->sut->login('valid_password', 'MOBILE');
    }

    /** @test */
    public function should_pass_all_validations_and_return_a_token()
    {
        $sut = Mockery::mock(User::class)->makePartial();
        $sut->fill([
            'email' => 'valid@valid.com',
            'password' => 'valid_password',
            'user_type' => 'CUSTOMER'
        ]);

        $device_name = 'MOBILE';
        
        $sut->shouldReceive('createToken')
            ->with($device_name)
            ->andReturn('valid_token');
        
        $token = $sut->login('valid_password', $device_name);
        
        $this->assertEquals('valid_token', $token);
    }

    /** @test */
    public function should_make_logout_and_return_true()
    {
        $sut = Mockery::mock(User::class)->makePartial();
        $sut->shouldReceive('tokens->delete')->andReturn(true);

        $isDeleted = $sut->logout();

        $this->assertTrue($isDeleted);
    }

    /** @test */
    public function should_make_logout_and_return_false()
    {
        $sut = Mockery::mock(User::class)->makePartial();
        $sut->shouldReceive('tokens->delete')->andReturn(false);

        $isDeleted = $sut->logout();

        $this->assertFalse($isDeleted);
    }

    /** @test */
    public function should_make_logout_and_return_false_if_delete_return_null()
    {
        $sut = Mockery::mock(User::class)->makePartial();
        $sut->shouldReceive('tokens->delete')->andReturn(null);

        $isDeleted = $sut->logout();

        $this->assertFalse($isDeleted);
    }
}
