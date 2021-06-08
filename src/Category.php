<?php


namespace Webdevils\Blog;

use Webdevils\Blog\Exceptions\InvalidCategory;
use Webdevils\Blog\Slug\SlugGenerator;

class Category
{
    use Validation;

    const MIN_NAME_LENGTH = 3;
    const MAX_NAME_LENGTH = 30;

    private Slug $slug;
    private string $name;

    private function __construct(Slug $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    public static function create(SlugGenerator $generator, string $name) : Category
    {
        if (self::isTooShort($name, self::MIN_NAME_LENGTH)) {
            throw new InvalidCategory('Category name must be minimum '.self::MIN_NAME_LENGTH.' characters');
        }
        if (self::isTooLong($name, self::MAX_NAME_LENGTH)) {
            throw new InvalidCategory('Category name must be maximum '.self::MAX_NAME_LENGTH.' characters');
        }

        return new Category(
            $generator->generate($name),
            $name
        );
    }

    public static function hydrate($slug, $name) : Category
    {
        return new Category($slug, $name);
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
