<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;
use Webdevils\Blog\Author;
use Webdevils\Blog\Exceptions\PublishError;
use Webdevils\Blog\Exceptions\ScheduleError;
use Webdevils\Blog\Slug;

class Published implements Status
{
    const NAME = 'published';

    private DateTimeImmutable $publishDate;
    private array $oldSlugs = [];

    private function __construct(DateTimeImmutable $publishDate)
    {
        $this->publishDate = $publishDate;
    }

    public static function create() : Published
    {
        return new Published(new DateTimeImmutable('now'));
    }

    public static function hydrate(DateTimeImmutable $publishDate) : Published
    {
        return new Published($publishDate);
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
        throw new PublishError('Cannot publish a blog post');
    }

    public function schedule(DateTimeImmutable $publishDate): Status
    {
        throw new ScheduleError('Cannot schedule a published blog post');
    }

    public function addOldSlug(Slug $slug): void
    {
        $this->oldSlugs[] = $slug;
    }

    public function getOldSlugs(): array
    {
        return $this->oldSlugs;
    }

    public function addAuthor(array $authors, Author $author): array
    {
        return $authors;
    }
}
