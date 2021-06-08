<?php


namespace Webdevils\Blog\Author;

use PDO;
use Webdevils\Blog\Author;

class DatabaseAuthorRepository implements AuthorRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function store(Author $author) : void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO authors (name) VALUES (?)'
        );
        $statement->execute([$author->getName()]);
    }

    public function find(string $name): Author
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM authors WHERE name = ?'
        );
        $statement->execute([$name]);

        $data = $statement->fetch();
        if ($data === false) {
            throw new AuthorNotFound('Author '.$name.' not found');
        }
        return new Author($data['name']);
    }
}
