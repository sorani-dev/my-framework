<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Database\Query;

use Sorani\SimpleFramework\Database\Query\QueryBuilder;
use Sorani\SimpleFramework\TestCase\DatabaseTestCase;

class QueryBuilderTest extends DatabaseTestCase
{
    /**
     * @var QueryBuilder
     */
    private QueryBuilder $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = new QueryBuilder();
    }

    public function testSimpleQuery()
    {
        $query = (new QueryBuilder())->from('posts')->fields('name');
        $this->assertEquals("SELECT name FROM posts;", (string)$query);
    }

    public function testtestWithWhereClause()
    {
        $query = (new QueryBuilder())->fields('name')->from('posts', 'p')->where("a = :a OR b = :b", "c = :c");
        $this->assertEquals("SELECT name FROM posts AS p WHERE (a = :a OR b = :b) AND (c = :c);", (string)$query);
        $query = (new QueryBuilder())
            ->fields('name')->from('posts', 'p')->from('category', 'c')->where("a = :a OR b = :b", "c = :c");
        $this->assertEquals(
            "SELECT name FROM posts AS p, category AS c WHERE (a = :a OR b = :b) AND (c = :c);",
            (string)$query
        );
    }

    public function testWithMultipleWhereClause()
    {
        $query = (new QueryBuilder())->from('comments', 'p')
            ->fields('username')
            ->where("a = :a OR b = :b")
            ->where("c = :c");
        $this->assertEquals(
            "SELECT username FROM comments AS p WHERE (a = :a OR b = :b) AND (c = :c);",
            (string)$query
        );
        $query2 = (new QueryBuilder())->from('comments', 'p')
            ->fields('username')
            ->where("a = :a OR b = :b", "c = :c");
        $this->assertEquals(
            "SELECT username FROM comments AS p WHERE (a = :a OR b = :b) AND (c = :c);",
            (string)$query2
        );
    }

    public function testFetchCount()
    {
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);

        $query = new QueryBuilder($pdo);
        $count = $query->from('posts', 'p')->count();
        $this->assertEquals(100, $count);
        $query = null;

        $query = new QueryBuilder($pdo);
        $count = $query->from('posts', 'p')
            ->where('p.id < :number')
            ->params(
                [
                    'number' => 30,
                ]
            )
            ->count();
        $this->assertEquals(29, $count);
    }
    public function testOrderBy()
    {
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->orderBy('id ASC');
        $this->assertEquals("SELECT * FROM comments ORDER BY id ASC;", (string)$query);

        $query = (new QueryBuilder($this->getPdo()))->from('comments')->orderBy('id ASC, name DESC');
        $this->assertEquals("SELECT * FROM comments ORDER BY id ASC, name DESC;", (string)$query);

        $query = (new QueryBuilder($this->getPdo()))->from('comments')->orderBy('id ASC')->orderBy('username DESC');
        $this->assertEquals("SELECT * FROM comments ORDER BY id ASC, username DESC;", (string)$query);


        $query = (new QueryBuilder($this->getPdo()))->from('comments')
            ->orderBy('id', QueryBuilder::ORDERBY_ASC)
            ->orderBy('username', QueryBuilder::ORDERBY_DESC);
        $this->assertEquals("SELECT * FROM comments ORDER BY id ASC, username DESC;", (string)$query);
    }

    public function testOrderByWithArray()
    {
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->orderBy(['id ASC', 'username DESC']);
        $this->assertEquals("SELECT * FROM comments ORDER BY id ASC, username DESC;", (string)$query);
    }


    /**
     * @group t
     */
    public function testLimit()
    {
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->limit(3);
        $this->assertEquals("SELECT * FROM comments LIMIT 3;", (string)$query);

        $this->expectException(\TypeError::class);
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->limit('3');
        $this->assertEquals("SELECT * FROM comments LIMIT 3;", (string)$query);

        $this->expectException(\TypeError::class);
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->limit('a');
    }

    /**
     * @group t
     */
    public function testOffsetWithOffset()
    {
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->limit(3)->offset(10);
        $this->assertEquals("SELECT * FROM comments LIMIT 3 OFFSET 10;", (string)$query);

        $this->expectException(\Exception::class);
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->limit(3)->offset(10.0);
        $this->assertEquals("SELECT * FROM comments LIMIT 3 OFFSET 10;", (string)$query);

        $this->expectException(\Exception::class);
        (new QueryBuilder($this->getPdo()))->from('comments')->limit(3)->offset('a');
    }
    public function testJoinQuery()
    {
        $query = (new QueryBuilder($this->getPdo()))
            ->from('comments', 'c')
            ->fields('username')
            ->joinByString('categories AS ca', 'ca.id = c.category_id')
            ->joinByString('categories AS ca2', 'ca2.id = c.category_id', 'INNER');
        $this->assertEquals(
            "SELECT username FROM comments AS c LEFT JOIN categories AS ca ON ca.id = c.category_id " .
                "INNER JOIN categories AS ca2 ON ca2.id = c.category_id;",
            (string)$query
        );
    }

    /**
     * @group t
     */
    public function testWithWhereIn()
    {
        $query = (new QueryBuilder($this->getPdo()))->from('comments', 'p')
            ->fields('username')
            ->where("a = :a OR b = :b", "c = :c")
            ->in("c", [1, 2, 3]);
        $this->assertEquals(
            "SELECT username FROM comments AS p WHERE (a = :a OR b = :b) AND (c = :c) AND (c IN (1, 2, 3));",
            (string)$query
        );
        $query = (new QueryBuilder($this->getPdo()))->from('comments', 'p')
            ->fields('username')
            ->where("a = :a OR b = :b")
            ->where("c = :c")
            ->in("c", clone $query);
        $this->assertEquals(
            "SELECT username FROM comments AS p WHERE (a = :a OR b = :b) AND (c = :c) "  .
            "AND (c IN ( SELECT username FROM comments AS p " .
            "WHERE (a = :a OR b = :b) AND (c = :c) AND (c IN (1, 2, 3)) ));",
            (string)$query
        );
    }

    public function testGroupBy()
    {
        $query = (new QueryBuilder($this->getPdo()))->from('employees')
            ->fields('department', 'COUNT(*) AS "Man_Power"')
            ->groupBy('department')
            ->having('COUNT(*) >= 10');
        $this->assertEquals(
            'SELECT department, COUNT(*) AS "Man_Power" FROM employees GROUP BY department HAVING COUNT(*) >= 10;',
            (string)$query
        );
    }
}
