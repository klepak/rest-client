<?php

namespace Klepak\RestClient\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Klepak\RestClient\RestClientServiceProvider;
use Symfony\Component\Process\Process;

abstract class TestCase extends BaseTestCase
{
    private static $webServer;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();


    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        //
    }

    /**
     * Get package providers.  At a minimum this is the package being tested, but also
     * would include packages upon which our package depends, e.g. Cartalyst/Sentry
     * In a normal app environment these would be added to the 'providers' array in
     * the config/app.php file.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            RestClientServiceProvider::class
        ];
    }
}
