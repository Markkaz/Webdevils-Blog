<?php

namespace Tests\Unit\Parsers;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Parsers\MarkdownParser;

class MarkdownParserTest extends TestCase
{
    /** @test */
    public function it_converts_markdown_to_html()
    {
        $parser = new MarkdownParser();

        $this->assertEquals(
            "<h1>Heading 1</h1>\n" .
            "<p>And a paragraph</p>\n" .
            "<p>Some <strong>bold</strong> and some <em>italic</em> text</p>\n" .
            "<ol>\n" .
            "<li>Short</li>\n" .
            "<li>List</li>\n" .
            "</ol>\n" .
            "<ul>\n" .
            "<li>Another</li>\n" .
            "<li>List</li>\n" .
            "</ul>\n" .
            "<p>And an image:</p>\n" .
            "<p><img src=\"random.png\" alt=\"Not found\" /></p>\n" .
            "<p>And a nice link to <a href=\"https://webdevils.nl\">Webdevils</a></p>\n",
            $parser->parse('
# Heading 1
And a paragraph
            
Some **bold** and some *italic* text
            
1. Short
2. List

- Another
- List

And an image:

![Not found](random.png)

And a nice link to [Webdevils](https://webdevils.nl)
            ')
        );
    }
}
