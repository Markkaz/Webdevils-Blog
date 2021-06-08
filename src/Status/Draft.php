<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;
use Webdevils\Blog\Author;
use Webdevils\Blog\Slug;

class Draft implements Status
{
    const NAME = 'draft';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getPublishDate(): ?DateTimeImmutable
    {
        return null;
    }

    public function publish(): Status
    {
        return Published::create();
    }

    public function schedule(DateTimeImmutable $publishDate): Status
    {
        return Scheduled::create($publishDate);
    }

    public function addOldSlug(Slug $slug): void
    {
    }

    public function getOldSlugs(): array
    {
        return [];
    }

    public function addAuthor(array $authors, Author $author): array
    {
        if (!in_array($author, $authors)) {
            $authors[] = $author;
        }

        return $authors;
    }
}
