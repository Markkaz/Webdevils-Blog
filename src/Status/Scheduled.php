<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;
use Webdevils\Blog\Author;
use Webdevils\Blog\Exceptions\ScheduleError;
use Webdevils\Blog\Slug;

class Scheduled implements Status
{
    const NAME = 'scheduled';

    private DateTimeImmutable $publishDate;

    private function __construct(DateTimeImmutable $publishDate)
    {
        $this->publishDate = $publishDate;
    }

    public static function create(DateTimeImmutable $publishDate) : Scheduled
    {
        if (new DateTimeImmutable('now') >= $publishDate) {
            throw new ScheduleError('Can only schedule a blog post in the future');
        }

        return new Scheduled($publishDate);
    }

    public static function hydrate(DateTimeImmutable $publishDate) : Scheduled
    {
        return new Scheduled($publishDate);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getPublishDate(): ?DateTimeImmutable
    {
        return $this->publishDate;
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
        return $authors;
    }
}
