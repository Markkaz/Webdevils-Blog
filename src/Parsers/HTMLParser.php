<?php


namespace Webdevils\Blog\Parsers;

use HTMLPurifier;
use HTMLPurifier_Config;

class HTMLParser implements Parser
{
    const NAME = 'html';

    public function parse(string $string): string
    {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($string);
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
