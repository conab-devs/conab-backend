<?php

namespace Tests\Unit;

use App\Components\Errors\UnprocessableEntityException;
use App\Components\Services\UserService;
use App\User;
use PHPUnit\Framework\TestCase;
use \Mockery;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/** @group Franklyn */
class UserServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function user_service_find_by_email_should_throw_error()
    {
        $this->expectException(UnprocessableEntityException::class);

        $model = Mockery::mock(User::class);
        $model->shouldReceive('where->first')->andReturn(null);

        $service = new UserService($model);
        $service->findByEmail('valid_email@mail.com');
    }

    /** @test */
    public function user_service_find_by_email_should_return_user()
    {
        $user = new User;
        $user->email = 'valid_email@mail.com';

        $model = Mockery::mock(User::class);
        $model->shouldReceive('where->first')->andReturn($user);

        $service = new UserService($model);
        $foundUser = $service->findByEmail($user->email);

        $this->assertEquals($user->email, $foundUser->email);
    }
}
