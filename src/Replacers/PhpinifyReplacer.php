<?php

namespace Netnak\Phpinify\Replacers;

use Illuminate\Http\Response;
use Statamic\StaticCaching\Replacer;
use Netnak\Phpinify\Phpinify;

class PhpinifyReplacer implements Replacer
{
	/**
	 * Minify HTML content before writing to the static cache.
	 */
	public function prepareResponseToCache(Response $response, Response $initial)
	{
		
        if (! config('phpinify.enable_static_cache_replacer', false)) {
			return;
		}

		$content = $response->getContent();

		if (empty($content) || stripos($content, '<html') === false) {
			return;
		}

		$minifiedContent = (new Phpinify($content))->getPhpinified();

		$response->setContent($minifiedContent);
	}

	/**
	 * This method is required by the interface but not used,
	 * as minification occurs before caching, not after retrieval.
	 */
	public function replaceInCachedResponse(Response $response): void
	{
		// Nothing to replace in cached response.
	}
}
