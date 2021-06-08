<?php


namespace Webdevils\Blog\Blogpost;

use Webdevils\Blog\BlogPost;
use Webdevils\Blog\Slug;

interface BlogPostRepository
{
    public function store(BlogPost $blogPost) : void;
    public function find(Slug $slug) : BlogPost;
}
