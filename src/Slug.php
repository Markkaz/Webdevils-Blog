<?php


namespace Webdevils\Blog;

use Webdevils\Blog\Exceptions\InvalidSlug;

class Slug
{
    private string $url;

    public function __construct(string $url)
    {
        if (!preg_match('/^[a-z0-9\-]*$/', $url)) {
            throw new InvalidSlug('Slug can only contain lower case letters, numbers and dashes (-)');
        }

        $this->url = $url;
    }


    public function getUrl() : string
    {
        return $this->url;
    }
}
