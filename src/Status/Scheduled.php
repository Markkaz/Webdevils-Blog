<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;
use Webdevils\Blog\Exceptions\ScheduleError;

class Scheduled implements Status
{
    const NAME = 'scheduled';

    private DateTimeImmutable $publishDate;

    public function __construct(DateTimeImmutable $publishDate)
    {
        if (new DateTimeImmutable('now') >= $publishDate) {
            throw new ScheduleError('Can only schedule a blog post in the future');
        }

        $this->publishDate = $publishDate;
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
        return new Published();
    }

    public function schedule(DateTimeImmutable $publishDate): Status
    {
        return new Scheduled($publishDate);
    }
}
