<?php

declare(strict_types=1);

namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Sorani\SimpleFramework\Database\Exception\NoRecordFoundException;
use Sorani\SimpleFramework\TestCase\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp(): void
    {
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNoRecordFound()
    {
        $this->expectException(NoRecordFoundException::class);
        $post = $this->postTable->find(100000);
        $this->assertNotInstanceOf(Post::class, $post);
        $this->assertNull($post);
    }

    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $this->postTable->update(1, ['name' => 'Hello', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Hello', $post->name);
        $this->assertEquals('demo', $post->slug);
    }

    public function testInsert()
    {
        $this->postTable->insert(
            [
                'name' => 'Hello',
                'slug' => 'demo',
                'content' => 'Tinkerbell singing in a tree',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
        $post = $this->postTable->find(1);
        $this->assertEquals('Hello', $post->name);
        $this->assertEquals('demo', $post->slug);
        $this->assertEquals('Tinkerbell singing in a tree', $post->content);
    }

    public function testDelete()
    {
        $post =
            [
                'name' => 'Hello',
                'slug' => 'demo',
                'content' => 'Tinkerbell singing in a tree',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        $this->postTable->insert($post);
        $this->postTable->insert($post);
        $this->assertEquals(2, (int)$this->postTable->getPdo()->query('SELECT COUNT(*) FROM posts')->fetchColumn());
        $this->postTable->delete((int)$this->postTable->getPdo()->lastInsertId());
        $this->assertEquals(1, (int)$this->postTable->getPdo()->query('SELECT COUNT(*) FROM posts')->fetchColumn());
    }
}
