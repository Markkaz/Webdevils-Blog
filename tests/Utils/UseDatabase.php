<?php


namespace Tests\Utils;

use PDO;

trait UseDatabase
{
    private static PDO $pdo;

    protected function getPdo() : PDO
    {
        return self::$pdo;
    }

    protected static function getTables() : array
    {
        return [
            'authors',
            'categories',
            'blogposts',
            'old_slugs',
            'blogposts_authors',
        ];
    }

    /** @beforeClass  */
    public static function initialiseDatabase() : void
    {
        self::createDatabaseConnection();
        self::createDatabaseTables();
    }

    private static function createDatabaseConnection() : void
    {
        self::$pdo = new PDO(
            sprintf('%s:dbname=%s;host=%s', $GLOBALS['DB_TYPE'], $GLOBALS['DB_NAME'], $GLOBALS['DB_HOST']),
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWORD']
        );
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    private static function createDatabaseTables(): void
    {
        foreach (self::getTables() as $table) {
            self::$pdo->exec(file_get_contents(
                dirname(__FILE__).'/../../database/'.$table.'.sql'
            ));
        }
    }

    /** @afterClass */
    public static function dropDatabaseTables(): void
    {
        self::$pdo->exec(
            'DROP TABLE ' . implode(', ', self::getTables())
        );
    }


    /** @before */
    protected function truncateDatabase() : void
    {
        self::$pdo->exec(
            'TRUNCATE TABLE ' . implode(', ', self::getTables())
        );
    }

    protected function assertDatabaseCount(string $table, int $count)
    {
        $statement = self::$pdo->query('SELECT COUNT(*) FROM '.$table);
        $actualCount = $statement->fetch()['count'];

        $this->assertEquals($count, $actualCount);
    }

    protected function assertDatabaseContains(string $table, array $values)
    {
        $statement = self::$pdo->query('SELECT * FROM '.$table);
        $results = $statement->fetchAll();

        $this->assertArrayContains($values, $results);
    }

    private function assertArrayContains(array $value, $results)
    {
        $this->assertTrue(
            in_array($value, $results),
            sprintf(
                'Failed asserting %s is in array %s',
                print_r($value, true),
                print_r($results, true),
            )
        );
    }
}
