<?php

declare(strict_types=1);

namespace Tests\Unit;

use function App\getenv;

final class GetEnvTest extends UnitTestCase
{
    public function test_it_returns_null_when_env_variable_is_not_set(): void
    {
        self::assertNull(getenv('NON_EXISTENT_ENV_VARIABLE'));
    }

    public function test_it_returns_integers_env_variables(): void
    {
        self::assertSame(123, getenv('INTEGER_ENV_VARIABLE'));
    }

    public function test_it_returns_float_env_variables(): void
    {
        self::assertSame(1.23, getenv('FLOAT_ENV_VARIABLE'));
    }

    public function test_it_returns_boolean_env_variables(): void
    {
        self::assertTrue(getenv('BOOLEAN_ENV_VARIABLE'));
    }
}
