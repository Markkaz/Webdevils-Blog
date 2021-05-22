<?php


namespace Webdevils\Blog\Status;

use DateTimeImmutable;

interface Status
{
    public function getName() : string;
    public function getPublishDate() : ?DateTimeImmutable;
    public function publish() : Status;
    public function schedule(DateTimeImmutable $publishDate) : Status;
}
