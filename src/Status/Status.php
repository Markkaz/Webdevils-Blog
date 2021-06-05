<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;
use Webdevils\Blog\Author;
use Webdevils\Blog\Slug;

interface Status
{
    public function getName() : string;
    public function getPublishDate() : ?DateTimeImmutable;
    public function publish() : Status;
    public function schedule(DateTimeImmutable $publishDate) : Status;
    public function addOldSlug(Slug $slug) : void;
    public function getOldSlugs() : array;
    public function addAuthor(array $authors, Author $author) : array;
}
