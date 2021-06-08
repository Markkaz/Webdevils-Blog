<?php

namespace Tests\Database;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Utils\UseDatabase;
use Webdevils\Blog\Author;
use Webdevils\Blog\Author\DatabaseAuthorRepository;
use Webdevils\Blog\BlogPost;
use Webdevils\Blog\Blogpost\BlogPostNotFound;
use Webdevils\Blog\Blogpost\DatabaseBlogPostRepository;
use Webdevils\Blog\Category;
use Webdevils\Blog\Category\DatabaseCategoryRepository;
use Webdevils\Blog\Parsers\HTMLParser;
use Webdevils\Blog\Slug;
use Webdevils\Blog\Slug\SlugGenerator;

class BlogPostRepositoryTest extends TestCase
{
    use UseDatabase;

    private DatabaseBlogPostRepository $repository;

    private function createGenerator() : SlugGenerator
    {
        return new SlugGenerator($this->repository);
    }

    private function createAuthor(string $name) : Author
    {
        $authorRepo = new DatabaseAuthorRepository($this->getPdo());
        $author = new Author($name);

        $authorRepo->store($author);

        return $author;
    }

    private function createCategory(string $name) : Category
    {
        $categoryRepo = new DatabaseCategoryRepository($this->getPdo());
        $category = Category::create(
            $this->createGenerator(),
            $name
        );

        $categoryRepo->store($category);

        return $category;
    }

    private function createBlogpost(string $title = 'New blogpost') : BlogPost
    {
        $blogPost = BlogPost::create(
            $this->createGenerator(),
            new HTMLParser(),
            $this->createAuthor('Mark'),
            $this->createCategory('PHP'),
            $title,
            'New introduction for a new blogpost',
            'Very long content for a new blogpost'
        );
        $this->repository->store($blogPost);

        return $blogPost;
    }

    protected function setUp() : void
    {
        $this->repository = new DatabaseBlogPostRepository($this->getPdo());
    }


    /** @test */
    public function can_store_a_blogpost()
    {
        $blogPost = BlogPost::create(
            $this->createGenerator(),
            new HTMLParser(),
            $this->createAuthor('Mark'),
            $this->createCategory('PHP'),
            'New Blogpost',
            'New blogpost with a new introduction',
            'New blogpost with a very long content',
        );

        $this->repository->store($blogPost);

        $this->assertDatabaseContainsBlogPost($blogPost);
        $this->assertDatabaseContains('blogposts_authors', [
            'blogpost_id' => $blogPost->getId(),
            'author_name' => 'Mark'
        ]);
    }

    /** @test */
    public function can_store_an_updated_blogpost()
    {
        $blogPost = $this->createBlogpost();
        $blogPost->update(
            author: $this->createAuthor('Andy'),
            title: 'Updated title',
            introduction: 'Updated introduction to the updated blogpost',
            content: 'Updated content to the updated blogpost'
        );

        $this->repository->store($blogPost);

        $this->assertDatabaseContainsBlogPost($blogPost);
        $this->assertDatabaseCount('blogposts', 1);

        $this->assertDatabaseContains('blogposts_authors', [
            'blogpost_id' => $blogPost->getId(),
            'author_name' => 'Mark'
        ]);
        $this->assertDatabaseContains('blogposts_authors', [
            'blogpost_id' => $blogPost->getId(),
            'author_name' => 'Andy'
        ]);
        $this->assertDatabaseCount('blogposts_authors', 2);
    }

    /** @test */
    public function can_store_a_published_blogpost()
    {
        $blogPost = $this->createBlogpost();
        $blogPost->publish();

        $blogPost->update(
            new Author('Andy'),
            title: 'New title',
            introduction: $blogPost->getIntroduction(),
            content: $blogPost->getContent()
        );

        $this->repository->store($blogPost);

        $this->assertDatabaseContainsBlogPost($blogPost);

        foreach ($blogPost->getOldSlugs() as $slug) {
            $this->assertDatabaseContains('old_slugs', [
                'slug' => $slug->getUrl(),
                'blogpost_id' => $blogPost->getId(),
            ]);
        }
    }

    /** @test */
    public function can_check_if_slug_exists()
    {
        $slug = new Slug('new-blogpost');

        $this->assertFalse($this->repository->exists($slug));

        $this->createBlogpost(
            title: 'New blogpost'
        );

        $this->assertTrue($this->repository->exists($slug));
    }

    /** @test */
    public function can_fetch_a_draft_blogpost()
    {
        $blogPost = $this->createBlogpost();

        $this->assertEquals(
            $blogPost,
            $this->repository->find($blogPost->getSlug())
        );
    }

    /** @test */
    public function can_fetch_a_scheduled_blogpost()
    {
        $blogPost = $this->createBlogpost();
        $blogPost->schedule(new DateTimeImmutable('+1 day'));
        $this->repository->store($blogPost);

        $this->assertEquals(
            $blogPost,
            $this->repository->find($blogPost->getSlug())
        );
    }

    /** @test */
    public function can_fetch_a_published_blogpost()
    {
        $blogPost = $this->createBlogpost();
        $blogPost->publish();
        $blogPost->update(
            $this->createAuthor('Andy'),
            'New Title',
            $blogPost->getIntroduction(),
            $blogPost->getContent()
        );

        $this->repository->store($blogPost);

        $actualBlogPost = $this->repository->find($blogPost->getSlug());
        $this->assertEquals(
            $blogPost,
            $actualBlogPost
        );
        $this->assertEquals(
            $blogPost->getOldSlugs(),
            $actualBlogPost
                ->getOldSlugs()
        );
    }

    /** @test */
    public function will_throw_an_exception_when_blogpost_doesnt_exist()
    {
        $this->expectException(BlogPostNotFound::class);

        $this->repository->find(new Slug('new-blogpost'));
    }

    private function assertDatabaseContainsBlogPost(BlogPost $blogPost): void
    {
        $this->assertDatabaseContains('blogposts', [
            'id' => $blogPost->getId(),
            'slug' => $blogPost->getSlug()->getUrl(),
            'category' => $blogPost->getCategory()->getSlug()->getUrl(),
            'title' => $blogPost->getTitle(),
            'introduction' => $blogPost->getIntroduction(),
            'content' => $blogPost->getContent(),
            'publish_date' => $blogPost->getPublishDate()
                ? $blogPost->getPublishDate()->format('Y-m-d H:i:s.u')
                : null,
            'status' => $blogPost->getStatus(),
            'parser' => $blogPost->getParserName(),
        ]);
    }
}
