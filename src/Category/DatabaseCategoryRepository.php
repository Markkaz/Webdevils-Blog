<?php


namespace Webdevils\Blog\Category;

use PDO;
use Webdevils\Blog\Category;
use Webdevils\Blog\Slug;
use Webdevils\Blog\Slug\SlugRepository;

class DatabaseCategoryRepository implements CategoryRepository, SlugRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(Category $category): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO categories (slug, name) values (?, ?)'
        );
        $statement->execute([
            $category->getSlug()->getUrl(),
            $category->getName()
        ]);
    }

    public function exists(Slug $slug): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT EXISTS(SELECT * FROM categories WHERE slug = ?);'
        );
        $statement->execute([$slug->getUrl()]);

        return $statement->fetch()['exists'];
    }

    public function find(Slug $slug): Category
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM categories WHERE slug = ?'
        );
        $statement->execute([$slug->getUrl()]);

        $categoryData = $statement->fetch();

        if ($categoryData === false) {
            throw new CategoryNotFound('Category with slug '.$slug->getUrl() . ' not found');
        }

        return Category::hydrate(
            new Slug($categoryData['slug']),
            $categoryData['name']
        );
    }
}
