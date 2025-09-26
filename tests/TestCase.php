<?php

namespace Lioy\Constella\Tests;

use Lioy\Constella\ConstellaServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {//
        return [
            ConstellaServiceProvider::class,
        ];
    }
}
