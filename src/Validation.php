<?php


namespace Webdevils\Blog;

trait Validation
{
    protected static function isTooShort(string $name, int $minChars): bool
    {
        return strlen($name) < $minChars;
    }

    protected static function isTooLong(string $name, int $maxChars): bool
    {
        return strlen($name) > $maxChars;
    }
}
