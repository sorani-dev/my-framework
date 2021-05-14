<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Database\Query;

use Sorani\SimpleFramework\Database\Query\QueryBuilder;
use Sorani\SimpleFramework\TestCase\DatabaseTestCase;
use Tests\Sorani\SimpleFramework\Database\fixtures\Demo;

class QueryBuilderTest extends DatabaseTestCase
{
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

        $this->expectException((phpversion() >= '7.2.0') ? \TypeError::class : \Exception::class);
        $query = (new QueryBuilder($this->getPdo()))->from('comments')->limit('3');
        $this->assertEquals("SELECT * FROM comments LIMIT 3;", (string)$query);

        $this->expectException((phpversion() >= '7.2.0') ? \TypeError::class : \Exception::class);
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

    public function testHydrateEntity()
    {
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);

        $query = new QueryBuilder($pdo);
        $posts = $query->from('posts', 'p')
            ->into(Demo::class)->fetchAll();
        $this->assertStringEndsWith('demo', $posts[0]->getSlug());//getUsername());
    }

    public function testIntoEntityIsTheSameInstance()
    {
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);

        $query = new QueryBuilder($pdo);
        $comments = $query->from('posts', 'p')
            ->where('p.id < :number')
            ->params(
                [
                    'number' => 30,
                ]
            )
            ->into(Demo::class)->fetchAll();
        $comment = $comments[0];
        $comment2 = $comments[0];
        $this->assertSame($comment, $comment2);
    }

    public function testSelectMultipleFrom()
    {
        $qb   = new QueryBuilder();

        $qb->fields('u.*')
        ->addFields('p.*')
        ->from('users', 'u')
            ->from('phonenumbers', 'p');

        $this->assertEquals('SELECT u.*, p.* FROM users AS u, phonenumbers AS p;', (string) $qb);
    }

    public function testClone()
    {
        $qb = new QueryBuilder();

        $qb->fields('u.id')
            ->from('users', 'u')
            ->where('u.id = :test');

        $qb->params([':test' => (object) 1]);

        $qbClone = clone $qb;

        $this->assertEquals((string) $qb, (string) $qbClone);

        $qb->where('u.id = 1');

        $this->assertNotSame($qb->queryToString(), $qbClone->queryToString());
    }

    public function testComplexSelectWithoutTableAliases()
    {
        $qb = new QueryBuilder();

        $qb->fields('DISTINCT users.id')
        ->from('users')
        ->from('articles')
        ->innerJoin('permissions', 'p', 'p.user_id = users.id')
        ->innerJoin('comments', 'c', 'c.article_id = articles.id')
        ->where('users.id = articles.user_id')
        ->where('p.read = 1');

        $this->assertEquals(
            'SELECT DISTINCT users.id FROM users, articles'
            . ' INNER JOIN permissions AS p ON p.user_id = users.id'
            . ' INNER JOIN comments AS c ON c.article_id = articles.id'
            . ' WHERE (users.id = articles.user_id) AND (p.read = 1);',
            $qb->queryToString()
        );
    }
}
