<?php

namespace Netnak\Phpinify\Tests\Unit;

use Netnak\Phpinify\Phpinify;
use Netnak\Phpinify\Tests\TestCase;

class PhpinifyTest extends TestCase
{
    public function test_minifies_html()
    {
        $html = <<<HTML
<html    lang="en" >  
  <body>  
    <!-- comment -->
    <h1>  Hello World    </h1>  
  </body>   
</html>
HTML;

        $phpinify = new Phpinify($html);
        $minified = $phpinify->getPhpinified();

        // Comments removed
        $this->assertStringNotContainsString('<!--', $minified);

        // No multiple spaces inside tags (attributes)
        $this->assertDoesNotMatchRegularExpression('/<[^>]+ {2,}[^>]*>/', $minified);

        // Multiple spaces inside text nodes are removed
        $this->assertStringContainsString('> Hello World <', $minified);

        // Spaces between tags are removed
        $this->assertStringNotContainsString('> <', $minified);

        // Tags remain intact
        $this->assertStringContainsString('</body>', $minified);
        $this->assertStringContainsString('</html>', $minified);
    }
}
