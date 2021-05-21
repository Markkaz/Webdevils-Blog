<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Category;
use Webdevils\Blog\Exceptions\InvalidCategory;

class CategoryTest extends TestCase
{
    /** @test */
    public function can_create_a_category()
    {
        $category = new Category(
            name: 'PHP'
        );

        $this->assertEquals('PHP', $category->getName());
    }

    /** @test */
    public function a_category_name_must_be_minimum_three_characters()
    {
        $this->expectException(InvalidCategory::class);

        new Category(
            name: 'No'
        );
    }

    /** @test */
    public function a_category_must_be_maximum_30_characters()
    {
        $this->expectException(InvalidCategory::class);

        new Category(
            name: 'A category with a too long name'
        );
    }
}
