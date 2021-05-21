<?php


namespace Webdevils\Blog;

trait Validation
{
    protected function isTooShort(string $name, int $minChars): bool
    {
        return strlen($name) < $minChars;
    }

    protected function isTooLong(string $name, int $maxChars): bool
    {
        return strlen($name) > $maxChars;
    }
}
