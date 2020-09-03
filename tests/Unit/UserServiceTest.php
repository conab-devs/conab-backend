<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use \Mockery;
use App\Components\Errors\UnprocessableEntityException;
use App\Components\Services\UserService;
use App\User;

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
}
