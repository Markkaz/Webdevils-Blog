<?php


namespace Webdevils\Blog;

use Webdevils\Blog\Exceptions\InvalidCategory;

class Category
{
    use Validation;

    const MIN_NAME_LENGTH = 3;
    const MAX_NAME_LENGTH = 30;

    private Slug $slug;
    private string $name;

    public function __construct(SlugGenerator $generator, string $name)
    {
        if ($this->isTooShort($name, self::MIN_NAME_LENGTH)) {
            throw new InvalidCategory('Category name must be minimum '.self::MIN_NAME_LENGTH.' characters');
        }
        if ($this->isTooLong($name, self::MAX_NAME_LENGTH)) {
            throw new InvalidCategory('Category name must be maximum '.self::MAX_NAME_LENGTH.' characters');
        }

        $this->slug = $generator->generate($name);
        $this->name = $name;
    }

    public function getSlug() : Slug
    {
        return $this->slug;
    }

    public function getName() : string
    {
        return $this->name;
    }
}
