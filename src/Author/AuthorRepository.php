<?php


namespace Webdevils\Blog\Author;

use Webdevils\Blog\Author;

interface AuthorRepository
{
    public function store(Author $author) : void;
    public function find(string $name) : Author;
}
