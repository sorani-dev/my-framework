<?php

declare(strict_types=1);

namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Tests\Sorani\SimpleFramework\Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }
    public function testFind()
    {
        $this->seedDatabase();
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNoRecordFound()
    {
        $post = $this->postTable->find(100000);
        $this->assertNotInstanceOf(Post::class, $post);
        $this->assertNull($post);
    }

    public function testUpdate()
    {
        $this->seedDatabase();
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
        $this->assertEquals(2, (int)$this->pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn());
        $this->postTable->delete((int)$this->pdo->lastInsertId());
        $this->assertEquals(1, (int)$this->pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn());
    }
}
