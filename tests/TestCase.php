<?php

namespace Netnak\Phpinify\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Netnak\Phpinify\ServiceProvider;

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
        $configPath = realpath(__DIR__ . '/../config/phpinify.php');
        $config = $configPath ? require $configPath : [];

       
        // Merge the config file values into the app config under 'phpinify'
        $app['config']->set('phpinify', $config);

        // Optionally override or add specific values here
       
        $app['config']->set('phpinify.enable_response_minifier', true);
        $app['config']->set('phpinify.enable_static_cache_replacer', true);
    

    }
}
