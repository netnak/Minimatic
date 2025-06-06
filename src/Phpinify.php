<?php

namespace Netnak\Phpinify;

use voku\helper\HtmlMin;

class Phpinify
{
    /**
     * The raw HTML to minify.
     *
     * @var string
     */
    protected string $html;

    /**
     * Create a new Phpinify instance.
     *
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->html = $html;
    }

    /**
     * Return the minified HTML.
     *
     * @return string
     */
    public function getPhpinified(): string
    {
        $htmlMin = new HtmlMin();

        $configArr = config('phpinify.funcs', []);

        // Apply each configured method dynamically to the HtmlMin object.
        foreach ($configArr as $method => $value) {
            if (method_exists($htmlMin, $method)) {
                $htmlMin->$method($value);
            }
        }

        return $htmlMin->minify($this->html);
    }
}
