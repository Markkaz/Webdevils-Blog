<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Slug;
use Webdevils\Blog\SlugGenerator;
use Webdevils\Blog\SlugRepository;

class SlugGeneratorTest extends TestCase
{
    private SlugGenerator $generator;
    private $repository;

    protected function setUp() : void
    {
        parent::setUp();

        $this->repository = $this->createStub(SlugRepository::class);
        $this->generator = new SlugGenerator($this->repository);
    }


    /** @test */
    public function generates_a_lowercase_slug()
    {
        $this->assertEquals(
            new Slug('blog'),
            $this->generator->generate('Blog')
        );
    }

    /** @test */
    public function replaces_spaces_with_dashes()
    {
        $this->assertEquals(
            new Slug('develop-a-blog'),
            $this->generator->generate('Develop a blog')
        );
    }

    /** @test */
    public function removes_special_characters()
    {
        $this->assertEquals(
            new Slug('develop-a-blog'),
            $this->generator->generate('Develop a blog!!!')
        );
    }

    /** @test */
    public function removes_trailing_spaces()
    {
        $this->assertEquals(
            new Slug('develop-a-blog'),
            $this->generator->generate('   Develop a blog   ')
        );
    }

    /** @test */
    public function adds_sequence_number_when_slug_already_exists()
    {
        $this->repository->method('exists')
            ->willReturn(true, false);

        $this->assertEquals(
            new Slug('develop-a-blog-2'),
            $this->generator->generate('Develop a Blog')
        );
    }

    /** @test */
    public function sequence_number_increases_when_previous_number_already_exists()
    {
        $this->repository->method('exists')
            ->willReturn(true, true, false);

        $this->assertEquals(
            new Slug('develop-a-blog-3'),
            $this->generator->generate('Develop a blog')
        );
    }
}
