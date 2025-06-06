<?php

namespace Netnak\Phpinify\Tests\Feature;

use Illuminate\Http\Request;
use Netnak\Phpinify\Http\Middleware\PhpinifyMiddleware;
use Netnak\Phpinify\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class MiddlewareTest extends TestCase
{
    public function test_middleware_minifies_html_response()
    {
        $middleware = new PhpinifyMiddleware();

        $request = Request::create('/');

        // Intentionally messy HTML with spaces around tags and comments
        $html = <<<HTML
<!DOCTYPE html>
<html >  <body>  
    <!-- This is a comment -->
    <h1>  Test       </h1>  
</body>   
</html>
HTML;

        $response = new Response($html);
        $response->headers->set('Content-Type', 'text/html');

        $next = function ($req) use ($response) {
            return $response;
        };

		
		// Handle the request through the middleware

        $response = $middleware->handle($request, $next);

        $content = $response->getContent();

        // 1) Comments should be removed
        $this->assertStringNotContainsString('<!--', $content, 'HTML comments should be removed');

        // 2) Spaces between tags should be removed (e.g., "> <" replaced with "><")
        $this->assertStringNotContainsString('> <', $content, 'Spaces between tags should be removed');

        // 3) No multiple spaces inside tags (e.g. <html  > or <body  >)
        $this->assertDoesNotMatchRegularExpression('/<[^>]+ {2,}[^>]*>/', $content, 'Multiple spaces inside tags should be removed');

        // 4) The tag structure should remain intact (basic check)
        $this->assertStringContainsString('</body>', $content);
        $this->assertStringContainsString('</html>', $content);

        // 6) Whitespace around tags is trimmed but text node spaces preserved
        $this->assertStringContainsString('<html><body><h1> Test </h1></body></html>', $content);
    }
}
