<?php


namespace Webdevils\Blog\Blogpost;

use DateTimeImmutable;
use PDO;
use PDOException;
use Webdevils\Blog\Author;
use Webdevils\Blog\BlogPost;
use Webdevils\Blog\Category;
use Webdevils\Blog\Parsers\Parser;
use Webdevils\Blog\Parsers\HTMLParser;
use Webdevils\Blog\Parsers\MarkdownParser;
use Webdevils\Blog\Slug;
use Webdevils\Blog\Slug\SlugGenerator;
use Webdevils\Blog\Slug\SlugRepository;
use Webdevils\Blog\Status\Draft;
use Webdevils\Blog\Status\Published;
use Webdevils\Blog\Status\Scheduled;
use Webdevils\Blog\Status\Status;

class DatabaseBlogPostRepository implements BlogPostRepository, SlugRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(BlogPost $blogPost): void
    {
        $this->pdo->beginTransaction();

        try {
            if ($blogPost->getId() === null) {
                $id = $this->storeBlogPost($blogPost);
                $blogPost->setId($id);
            } else {
                $this->updateBlogPost($blogPost);
                $this->deleteAuthors($blogPost->getId());
                $this->deleteOldSlugs($blogPost->getId());
            }

            $this->storeBlogPostAuthors($blogPost->getAuthors(), $blogPost->getId());
            $this->storeOldSlugs($blogPost->getOldSlugs(), $blogPost->getId());
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        $this->pdo->commit();
    }

    private function deleteOldSlugs(string $id)
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM old_slugs WHERE blogpost_id = ?'
        );
        $statement->execute([$id]);
    }

    private function storeOldSlugs(array $slugs, string $id)
    {
        foreach ($slugs as $slug) {
            $statement = $this->pdo->prepare(
                'INSERT INTO old_slugs ' .
                    '(slug, blogpost_id) ' .
                'VALUES ' .
                    '(?, ?);'
            );
            $statement->execute([$slug->getUrl(), $id]);
        }
    }

    private function deleteAuthors(string $id)
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM blogposts_authors WHERE blogpost_id = ?'
        );
        $statement->execute([$id]);
    }

    private function updateBlogPost(BlogPost $blogPost) : void
    {
        $statement = $this->pdo->prepare(
            'UPDATE blogposts SET '.
                'slug = ?, ' .
                'title = ?, ' .
                'introduction = ?, ' .
                'content = ?, ' .
                'status = ?, ' .
                'publish_date = ?' .
            'WHERE id = ?;'
        );
        $statement->execute([
            $blogPost->getSlug()->getUrl(),
            $blogPost->getTitle(),
            $blogPost->getIntroduction(),
            $blogPost->getContent(),
            $blogPost->getStatus(),
            $blogPost->getPublishDate()
                ? $blogPost->getPublishDate()->format('Y-m-d H:i:s.u')
                : null,
            $blogPost->getId(),
        ]);
    }

    private function storeBlogPost(BlogPost $blogPost): string
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO blogposts ' .
                '(slug, category, title, introduction, content, status, parser)' .
            'VALUES' .
                '(?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $blogPost->getSlug()->getUrl(),
            $blogPost->getCategory()->getSlug()->getUrl(),
            $blogPost->getTitle(),
            $blogPost->getIntroduction(),
            $blogPost->getContent(),
            $blogPost->getStatus(),
            $blogPost->getParserName()
        ]);

        return $this->pdo->lastInsertId('blogposts_id_seq');
    }

    private function storeBlogPostAuthors(array $authors, string $id): void
    {
        foreach ($authors as $author) {
            $statement = $this->pdo->prepare(
                'INSERT INTO blogposts_authors ' .
                '(blogpost_id, author_name) ' .
                'VALUES ' .
                '(?, ?)'
            );
            $statement->execute([
                $id,
                $author->getName(),
            ]);
        }
    }

    public function exists(Slug $slug): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT EXISTS(SELECT * FROM blogposts WHERE slug = ?)'
        );
        $statement->execute([$slug->getUrl()]);

        return $statement->fetch()['exists'];
    }

    public function find(Slug $slug): BlogPost
    {
        $blogPostData = $this->findBlogPosts($slug);

        if ($blogPostData === false) {
            throw new BlogPostNotFound('BlogPost with slug ' . $slug->getUrl() . ' not found');
        }

        $authors = $this->findAuthors($blogPostData['id']);

        return $this->createBlogPost($blogPostData, $authors);
    }

    private function findAuthors(string $id): array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM blogposts_authors WHERE blogpost_id = ?'
        );
        $statement->execute([$id]);

        return array_map(function ($author) {
            return new Author($author['author_name']);
        }, $statement->fetchAll());
    }

    private function findBlogPosts(Slug $slug): array|bool
    {
        $statement = $this->pdo->prepare(
            'SELECT blogposts.*, categories.name as category_name ' .
            'FROM blogposts ' .
            'JOIN categories ON categories.slug = blogposts.category ' .
            'WHERE blogposts.slug = ?'
        );
        $statement->execute([$slug->getUrl()]);

        return $statement->fetch();
    }

    private function createBlogPost(array $blogPostData, array $authors): BlogPost
    {
        $status = $this->getStatus(
            $blogPostData['status'],
            $blogPostData['publish_date']
        );
        $this->addOldSlugs($blogPostData['id'], $status);

        return BlogPost::hydrate(
            id: $blogPostData['id'],
            generator: new SlugGenerator($this),
            slug: new Slug($blogPostData['slug']),
            authors: $authors,
            category: Category::hydrate(
                new Slug($blogPostData['category']),
                $blogPostData['category_name']
            ),
            title: $blogPostData['title'],
            introduction: $blogPostData['introduction'],
            content: $blogPostData['content'],
            status: $status,
            parser: $this->getParser($blogPostData['parser'])
        );
    }

    private function addOldSlugs(string $id, Status $status) : void
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM old_slugs WHERE blogpost_id = ?'
        );
        $statement->execute([$id]);
        $slugs = $statement->fetchAll();

        foreach ($slugs as $slug) {
            $status->addOldSlug(
                new Slug($slug['slug'])
            );
        }
    }

    private function getParser(string $parser) : Parser
    {
        $parsers = [
            HTMLParser::NAME => new HTMLParser(),
            MarkdownParser::NAME => new MarkdownParser(),
        ];

        return $parsers[$parser];
    }

    private function getStatus(string $status, ?string $publishDate) : Status
    {
        switch ($status) {
            case Draft::NAME:
                return new Draft();

            case Scheduled::NAME:
                return Scheduled::hydrate(new DateTimeImmutable($publishDate));

            case Published::NAME:
                return Published::hydrate(new DateTimeImmutable($publishDate));
        }
    }
}
