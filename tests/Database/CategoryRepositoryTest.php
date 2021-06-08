<?php

namespace Tests\Database;

use PHPUnit\Framework\TestCase;
use Tests\Utils\UseDatabase;
use Webdevils\Blog\Category;
use Webdevils\Blog\Category\CategoryNotFound;
use Webdevils\Blog\Category\DatabaseCategoryRepository;
use Webdevils\Blog\Slug;
use Webdevils\Blog\Slug\SlugGenerator;

class CategoryRepositoryTest extends TestCase
{
    use UseDatabase;

    private DatabaseCategoryRepository $repository;

    protected function createGenerator(): SlugGenerator
    {
        return new SlugGenerator(
            $this->repository
        );
    }

    protected function createCategory($name) : Category
    {
        $category = Category::create(
            $this->createGenerator(),
            $name
        );
        $this->repository->store($category);

        return $category;
    }

    protected function setUp(): void
    {
        $this->repository = new DatabaseCategoryRepository($this->getPdo());
    }

    /** @test */
    public function can_store_a_category()
    {
        $category = Category::create(
            generator: $this->createGenerator(),
            name: 'New category'
        );

        $this->repository->store($category);

        $this->assertDatabaseContains(
            'categories',
            [
                'slug' => $category->getSlug()->getUrl(),
                'name' => $category->getName()
            ]
        );
    }

    /** @test */
    public function can_check_if_slug_exists()
    {
        $slug = new Slug('new-category');

        $this->assertFalse($this->repository->exists($slug));

        $this->createCategory('New category');

        $this->assertTrue($this->repository->exists($slug));
    }

    /** @test */
    public function can_fetch_a_category()
    {
        $category = $this->createCategory('PHP');

        $this->assertEquals(
            $category,
            $this->repository->find(new Slug('php'))
        );
    }

    /** @test */
    public function gives_an_exception_when_category_doesnt_exist()
    {
        $this->expectException(CategoryNotFound::class);

        $this->repository->find(new Slug('php'));
    }
}
