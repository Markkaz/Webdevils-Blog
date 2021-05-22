<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;
use Webdevils\Blog\Exceptions\PublishError;
use Webdevils\Blog\Exceptions\ScheduleError;

class Published implements Status
{
    const NAME = 'published';

    private DateTimeImmutable $publishDate;

    public function __construct()
    {
        $this->publishDate = new DateTimeImmutable('now');
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
}
