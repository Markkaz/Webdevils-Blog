<?php

namespace Tests\Database;

use PHPUnit\Framework\TestCase;
use Tests\Utils\UseDatabase;
use Webdevils\Blog\Author;
use Webdevils\Blog\Author\AuthorNotFound;
use Webdevils\Blog\Author\AuthorRepository;
use Webdevils\Blog\Author\DatabaseAuthorRepository;

class AuthorRepositoryTest extends TestCase
{
    use UseDatabase;

    private AuthorRepository $repository;

    protected function setUp() : void
    {
        $this->repository = new DatabaseAuthorRepository(
            $this->getPdo()
        );
    }

    protected function createAuthor($name) : Author
    {
        $author = new Author($name);
        $this->repository->store($author);

        return $author;
    }

    /** @test */
    public function can_store_a_new_author()
    {
        $author = new Author('Mark');

        $this->repository->store($author);

        $this->assertDatabaseContains('authors', ['name' => 'Mark']);
        $this->assertDatabaseCount('authors', 1);
    }

    /** @test */
    public function can_fetch_an_author()
    {
        $author = $this->createAuthor('Mark');

        $fetchedAuthor = $this->repository->find('Mark');

        $this->assertEquals($author, $fetchedAuthor);
    }

    /** @test */
    public function throws_exception_when_fetching_a_non_existent_author()
    {
        $this->expectException(AuthorNotFound::class);

        $repository = new DatabaseAuthorRepository($this->getPdo());
        $repository->find('Mark');
    }
}
