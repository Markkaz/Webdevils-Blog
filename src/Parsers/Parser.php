<?php


namespace Webdevils\Blog\Parsers;

interface Parser
{
    public function parse(string $string) : string;
    public function getName() : string;
}
