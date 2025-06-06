<?php

namespace Netnak\Minimatic;

use Statamic\Providers\AddonServiceProvider;
use Netnak\Minimatic\Http\Middleware\MinimaticMiddleware;
use Netnak\Minimatic\Replacers\MinimaticReplacer;

class ServiceProvider extends AddonServiceProvider
{
    /**
     * Automatically merge/publish this config file under the 'minimatic' key.
     */
    protected $config = __DIR__ . '/../config/minimatic.php';

    /**
     * Middleware applied to the 'web' group.
     */
    protected $middlewareGroups = [
        'web' => [
            MinimaticMiddleware::class,
        ],
    ];


    /**
     * Boot the addon.
     */
    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/minimatic.php', 'minimatic');

        $this->registerReplacer();

        // php artisan vendor:publish --tag=minimatic-config --force  
        $this->publishes([
            __DIR__ . '/../config/minimatic.php' => config_path('minimatic.php'),
        ], 'minimatic-config');
    }

    /**
     * Dynamically inject the replacer unless disabled in the config.
     */
    protected function registerReplacer()
    {
        $enabled = config('minimatic.enable_static_cache_replacer', true);

        if (! $enabled) {
            return;
        }

        $replacers = config('statamic.static_caching.replacers', []);

        if (!in_array(MinimaticReplacer::class, $replacers)) {
            $replacers[] = MinimaticReplacer::class;
            config(['statamic.static_caching.replacers' => $replacers]);
        }
    }
}
