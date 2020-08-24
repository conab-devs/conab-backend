<?php

namespace Tests\Unit;

use App\User;
use App\Components\Errors\InvalidFieldException;
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

        $sut->login();
    }
}
