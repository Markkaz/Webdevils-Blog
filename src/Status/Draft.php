<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;

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
        return new Published();
    }

    public function schedule(DateTimeImmutable $publishDate): Status
    {
        return new Scheduled($publishDate);
    }
}
