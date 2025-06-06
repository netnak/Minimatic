<?php

namespace Netnak\Minimatic\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Netnak\Minimatic\ServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $configPath = realpath(__DIR__ . '/../config/minimatic.php');
        $config = $configPath ? require $configPath : [];

       
        // Merge the config file values into the app config under 'minimatic'
        $app['config']->set('minimatic', $config);

        // Optionally override or add specific values here
       
        $app['config']->set('minimatic.enable_response_minifier', true);
        $app['config']->set('minimatic.enable_static_cache_replacer', true);
    

    }
}
