<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Author;
use Webdevils\Blog\Exceptions\InvalidAuthor;

class AuthorTest extends TestCase
{
    /** @test */
    public function can_create_author()
    {
        $author = new Author(
            name: 'Mark',
        );

        $this->assertEquals('Mark', $author->getName());
    }

    /** @test */
    public function an_author_needs_a_name()
    {
        $this->expectException(InvalidAuthor::class);

        new Author(
            name: ''
        );
    }

    /** @test */
    public function the_name_cannot_be_too_long()
    {
        $this->expectException(InvalidAuthor::class);

        new Author(
            name: 'A very long name with more than 30 characters'
        );
    }
}
