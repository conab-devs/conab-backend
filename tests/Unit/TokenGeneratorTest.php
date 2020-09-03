<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Components\TokenGenerator;

/** @author Franklyn */
class TokenGeneratorTest extends TestCase
{
    /** @test */
    public function token_generator_should_return_token()
    {
        $sut = new TokenGenerator();

        $token = $sut->generate();

        $this->assertNotEmpty($token);
    }
}
