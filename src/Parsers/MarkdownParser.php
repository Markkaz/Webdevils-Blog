<?php


namespace Webdevils\Blog\Parsers;

use League\CommonMark\CommonMarkConverter;

class MarkdownParser implements Parser
{
    const NAME = 'markdown';

    public function parse(string $string): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 5
        ]);
        return $converter->convertToHtml($string);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
