<?php


namespace Webdevils\Blog;

interface SlugRepository
{
    public function exists(Slug $slug) : bool;
}
