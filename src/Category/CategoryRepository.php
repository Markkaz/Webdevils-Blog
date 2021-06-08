<?php


namespace Webdevils\Blog\Category;

use Webdevils\Blog\Category;
use Webdevils\Blog\Slug;

interface CategoryRepository
{
    public function store(Category $category) : void;
    public function find(Slug $slug) : Category;
}
