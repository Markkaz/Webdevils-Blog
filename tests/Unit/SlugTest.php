<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Exceptions\InvalidSlug;
use Webdevils\Blog\Slug;

class SlugTest extends TestCase
{
    /** @test */
    public function can_create_a_new_slug()
    {
        $slug = new Slug('develop-a-blog');

        $this->assertEquals('develop-a-blog', $slug->getUrl());
    }

    /** @test */
    public function can_contain_a_number()
    {
        $slug = new Slug('develop-a-blog-2');

        $this->assertEquals('develop-a-blog-2', $slug->getUrl());
    }

    /** @test */
    public function cannot_contain_capitals_and_underscores()
    {
        $this->expectException(InvalidSlug::class);

        new Slug('Develop_A_Blog');
    }

    /** @test */
    public function cannot_contain_special_characters()
    {
        $this->expectException(InvalidSlug::class);

        new Slug('the-cost-in-$-of-a-house');
    }
}
