<?php

namespace Netnak\Minimatic\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Netnak\Minimatic\Minimatic;

class MinimaticMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        
        $response = $next($request);

        if (! config('minimatic.enable_response_minifier', false)) {
            return $response;
        }


        if ($this->shouldIgnore($request)) {
            return $response;
        }

        if ($this->isHtmlResponse($response)) {

            $content = $response->getContent();

            if (! empty($content) && stripos($content, '<html') !== false) {
                $minified = (new Minimatic($content))->getPhpinified();
                $response->setContent($minified);
            }
        }
       
        
        
        return $response;
    }

    /**
     * Determine if the request should be ignored based on config.
     */
    protected function shouldIgnore(Request $request): bool
    {
        $ignored = config('minimatic.ignored_paths', ['!/*']);
        foreach ($ignored as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the response is HTML and can be minified.
     */
    protected function isHtmlResponse($response): bool
    {
        return method_exists($response, 'getContent') &&
               str_contains(strtolower($response->headers->get('Content-Type', '')), 'text/html');
    }
}
