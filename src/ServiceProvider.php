<?php

namespace Netnak\Phpinify;

use Statamic\Providers\AddonServiceProvider;
use Netnak\Phpinify\Http\Middleware\PhpinifyMiddleware;
use Netnak\Phpinify\Replacers\PhpinifyReplacer;

class ServiceProvider extends AddonServiceProvider
{
    /**
     * Automatically merge/publish this config file under the 'phpinify' key.
     */
    protected $config = __DIR__ . '/../config/phpinify.php';

    /**
     * Middleware applied to the 'web' group.
     */
    protected $middlewareGroups = [
        'web' => [
            PhpinifyMiddleware::class,
        ],
    ];


    /**
     * Boot the addon.
     */
    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/phpinify.php', 'phpinify');

        $this->registerReplacer();

        // php artisan vendor:publish --tag=phpinify-config --force  
        $this->publishes([
            __DIR__ . '/../config/phpinify.php' => config_path('phpinify.php'),
        ], 'phpinify-config');
    }

    /**
     * Dynamically inject the replacer unless disabled in the config.
     */
    protected function registerReplacer()
    {
        $enabled = config('phpinify.enable_static_cache_replacer', true);

        if (! $enabled) {
            return;
        }

        $replacers = config('statamic.static_caching.replacers', []);

        if (!in_array(PhpinifyReplacer::class, $replacers)) {
            $replacers[] = PhpinifyReplacer::class;
            config(['statamic.static_caching.replacers' => $replacers]);
        }
    }
}
