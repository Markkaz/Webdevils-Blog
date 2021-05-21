<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Author;
use Webdevils\Blog\BlogPost;
use Webdevils\Blog\Category;
use Webdevils\Blog\Exceptions\InvalidBlogPost;

class BlogPostTest extends TestCase
{
    protected function createBlogPost(
        string $title = 'My first blog post',
        string $introduction = 'A short introduction to the BlogPost',
        string $content = 'The content of the full article',
    ): BlogPost {
        return new BlogPost(
            author: new Author('Mark'),
            category: new Category('PHP'),
            title: $title,
            introduction: $introduction,
            content: $content,
        );
    }

    /** @test */
    public function can_create_a_new_blogpost()
    {
        $blogPost = $this->createBlogPost();

        $this->assertEquals(new Author('Mark'), $blogPost->getAuthor());
        $this->assertEquals(new Category('PHP'), $blogPost->getCategory());
        $this->assertEquals('My first blog post', $blogPost->getTitle());
        $this->assertEquals('A short introduction to the BlogPost', $blogPost->getIntroduction());
        $this->assertEquals('The content of the full article', $blogPost->getContent());
    }

    /** @test */
    public function a_blog_post_must_be_valid()
    {
        try {
            $this->createBlogPost(
                title: '',
                introduction: '',
                content: '',
            );

            $this->fail('Expected InvalidBlogPost exception: BlogPost is invalid');
        } catch (InvalidBlogPost $e) {
            $this->assertCount(3, $e->getErrors());
        }
    }

    /** @test */
    public function the_title_must_be_minimum_3_characters()
    {
        $this->expectException(InvalidBlogPost::class);

        $this->createBlogPost(title: 'Oh');
    }

    /** @test */
    public function the_title_must_be_maximum_70_characters()
    {
        $this->expectException(InvalidBlogPost::class);

        $this->createBlogPost(title: 'A title that is way too long for a sane Blog post. This should be invalid!');
    }

    /** @test */
    public function the_introduction_must_be_minimum_25_characters()
    {
        $this->expectException(InvalidBlogPost::class);

        $this->createBlogPost(introduction: 'Too short');
    }

    /** @test */
    public function the_content_must_be_minimum_25_characters()
    {
        $this->expectException(InvalidBlogPost::class);

        $this->createBlogPost(content: 'Too short');
    }
}
