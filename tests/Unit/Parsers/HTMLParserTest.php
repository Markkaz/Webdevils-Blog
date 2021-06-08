<?php

namespace Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Parsers\HTMLParser;

class HTMLParserTest extends TestCase
{
    /** @test */
    public function it_cleans_html()
    {
        $parser = new HTMLParser();

        $this->assertEquals(
            '<h1>Header</h1>' .
            '<p>Next line is a paragraph</p>' .
            '<p><a>Test</a> A link to <a href="https://webdevils.nl">Webdevils.nl</a>' .
            '</p><ul>' .
            '<li>Item 1</li>' .
            '</ul>',
            $parser->parse(
                '<h1>Header</h1>' .
                '<script>alert(\'xss\')</script>' .
                '<p>Next line is a paragraph</p>' .
                '<p><a href="javascript:alert(\'boe!\')">Test</a> A link to <a href="https://webdevils.nl">Webdevils.nl</a>' .
                '<ul>' .
                '<li>Item 1'
            )
        );
    }
}
