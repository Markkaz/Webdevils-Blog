<?php

namespace Tests;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Webdevils\Blog\Author;
use Webdevils\Blog\BlogPost;
use Webdevils\Blog\Category;
use Webdevils\Blog\Exceptions\InvalidBlogPost;
use Webdevils\Blog\Exceptions\PublishError;
use Webdevils\Blog\Exceptions\ScheduleError;
use Webdevils\Blog\Parsers\Parser;
use Webdevils\Blog\Status\Draft;
use Webdevils\Blog\Status\Published;
use Webdevils\Blog\Status\Scheduled;

class BlogPostTest extends TestCase
{
    protected function createBlogPost(
        string $title = 'My first blog post',
        string $introduction = 'A short introduction to the BlogPost',
        string $content = 'The content of the full article',
        Parser $parser = null
    ): BlogPost {
        if ($parser == null) {
            $parser = $this->createStub(Parser::class);
            $parser->method('parse')
                ->will($this->returnArgument(0));
        }

        return new BlogPost(
            parser: $parser,
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
    public function a_new_blogpost_is_in_draft()
    {
        $blogPost = $this->createBlogPost();

        $this->assertEquals(Draft::NAME, $blogPost->getStatus());
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

    /** @test */
    public function a_draft_blog_post_can_be_scheduled()
    {
        $tomorrow = new DateTimeImmutable('tomorrow');

        $blogPost = $this->createBlogPost();
        $blogPost->schedule($tomorrow);

        $this->assertEquals(Scheduled::NAME, $blogPost->getStatus());
        $this->assertEquals($tomorrow, $blogPost->getPublishDate());
    }

    /** @test */
    public function can_only_schedule_a_blogpost_in_the_future()
    {
        $this->expectException(ScheduleError::class);

        $blogPost = $this->createBlogPost();
        $blogPost->schedule(
            publishDate: new DateTimeImmutable('yesterday')
        );
    }

    /** @test */
    public function a_draft_blog_post_can_be_published()
    {
        $blogPost = $this->createBlogPost();
        $blogPost->publish();

        $this->assertEquals(Published::NAME, $blogPost->getStatus());
        $this->assertEqualsWithDelta(
            (new DateTimeImmutable('now'))->getTimestamp(),
            $blogPost->getPublishDate()->getTimestamp(),
            1
        );
    }

    /** @test */
    public function a_draft_blog_post_doesnt_have_a_publish_date()
    {
        $blogPost = $this->createBlogPost();
        $this->assertNull($blogPost->getPublishDate());
    }

    /** @test */
    public function a_scheduled_blog_post_can_be_rescheduled()
    {
        $nextWeek = new DateTimeImmutable('+1 week');

        $blogPost = $this->createBlogPost();
        $blogPost->schedule(new DateTimeImmutable('tomorrow'));
        $blogPost->schedule($nextWeek);

        $this->assertEquals(Scheduled::NAME, $blogPost->getStatus());
        $this->assertEquals($blogPost->getPublishDate(), $nextWeek);
    }

    /** @test */
    public function a_scheduled_blog_post_can_be_published()
    {
        $blogPost = $this->createBlogPost();
        $blogPost->schedule(new DateTimeImmutable('tomorrow'));
        $blogPost->publish();

        $this->assertEquals(Published::NAME, $blogPost->getStatus());
        $this->assertEqualsWithDelta(
            (new DateTimeImmutable('now'))->getTimestamp(),
            $blogPost->getPublishDate()->getTimestamp(),
            1
        );
    }

    /** @test */
    public function a_published_blog_post_cannot_be_scheduled()
    {
        $this->expectException(ScheduleError::class);

        $blogPost = $this->createBlogPost();
        $blogPost->publish();
        $blogPost->schedule(new DateTimeImmutable('tomorrow'));

        $this->assertEquals(Published::NAME, $blogPost->getStatus());
        $this->assertEqualsWithDelta(
            (new DateTimeImmutable('now'))->getTimestamp(),
            $blogPost->getPublishDate(),
            1
        );
    }

    /** @test */
    public function a_published_blog_cannot_be_published_again()
    {
        $this->expectException(PublishError::class);

        $blogPost = $this->createBlogPost();
        $blogPost->publish();
        $blogPost->publish();
    }

    /** @test */
    public function the_introduction_is_parsed()
    {
        $parser = $this->createStub(Parser::class);
        $parser->method('parse')
            ->willReturn('Parsed introduction');

        $blogPost = $this->createBlogPost(parser: $parser);

        $this->assertEquals('Parsed introduction', $blogPost->getIntroduction());
    }

    /** @test */
    public function the_content_is_parsed()
    {
        $parser = $this->createStub(Parser::class);
        $parser->method('parse')
            ->willReturn('Parsed content');

        $blogPost = $this->createBlogPost(
            parser: $parser,
        );
        $this->assertEquals('Parsed content', $blogPost->getContent());
    }
}
