<?php

namespace Xinax\LaravelGettext\Testing;

use Orchestra\Testbench\TestCase;
use Xinax\LaravelGettext\LaravelGettextServiceProvider;

/**
 * Base test case for package tests.
 * Boots a Laravel application through Orchestra Testbench
 * with the package service provider registered.
 */
class BaseTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelGettextServiceProvider::class,
        ];
    }
}
