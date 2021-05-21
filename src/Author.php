<?php


namespace Webdevils\Blog;

use Webdevils\Blog\Exceptions\InvalidAuthor;

class Author
{
    use Validation;

    const MIN_NAME_LENGTH = 1;
    const MAX_NAME_LENGTH = 30;

    private string $name;

    public function __construct(string $name)
    {
        if ($this->isTooShort($name, self::MIN_NAME_LENGTH)) {
            throw new InvalidAuthor('Name must be minimum '.self::MIN_NAME_LENGTH.' characters long');
        }
        if ($this->isTooLong($name, self::MAX_NAME_LENGTH)) {
            throw new InvalidAuthor('Name must be maximum '.self::MAX_NAME_LENGTH.' characters long');
        }

        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }
}
