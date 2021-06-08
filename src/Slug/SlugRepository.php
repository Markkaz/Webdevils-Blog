<?php


namespace Webdevils\Blog\Slug;

use Webdevils\Blog\Slug;

interface SlugRepository
{
    public function exists(Slug $slug) : bool;
}
