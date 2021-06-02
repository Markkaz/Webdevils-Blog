<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Category;
use Webdevils\Blog\Exceptions\InvalidCategory;
use Webdevils\Blog\Slug;
use Webdevils\Blog\SlugGenerator;

class CategoryTest extends TestCase
{
    protected function createCategory(
        ?SlugGenerator $generator = null,
        string $name = 'PHP',
        ?string $slug = null
    ) : Category {
        if ($slug === null) {
            $slug = strtolower($slug);
        }

        if ($generator === null) {
            $generator = $this->createStub(SlugGenerator::class);
            $generator->method('generate')
                ->willReturn(new Slug($slug));
        }

        return new Category(
            $generator,
            $name
        );
    }


    /** @test */
    public function can_create_a_category()
    {
        $category = $this->createCategory();
        $this->assertEquals('PHP', $category->getName());
    }

    /** @test */
    public function a_category_generates_a_slug()
    {
        $generator = $this->createMock(SlugGenerator::class);
        $generator->expects($this->once())
            ->method('generate')
            ->with($this->equalTo('PHP'))
            ->willReturn(new Slug('php'));

        $category = $this->createCategory(
            generator: $generator
        );

        $this->assertEquals(
            new Slug('php'),
            $category->getSlug()
        );
    }

    /** @test */
    public function a_category_name_must_be_minimum_three_characters()
    {
        $this->expectException(InvalidCategory::class);

        $this->createCategory(
            name: 'no',
        );
    }

    /** @test */
    public function a_category_must_be_maximum_30_characters()
    {
        $this->expectException(InvalidCategory::class);

        $this->createCategory(
            name: 'A category with a too long name',
        );
    }
}
