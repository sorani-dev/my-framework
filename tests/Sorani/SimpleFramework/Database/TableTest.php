<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Database;

use Sorani\SimpleFramework\Database\Table;
use Sorani\SimpleFramework\TestCase\ExtendedTestCase;

class TableTest extends ExtendedTestCase
{
    /**
     * @var Table
     */
    private $table;

    protected function setUp(): void
    {

        // create PDO instance
        // $pdo = new \PDO('sqlite::memory:', null, null, [
        //     \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        // ]);

        // $pdo->exec('CREATE TABLE comments ("id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, "name" TEXT NOT NULL);');
        $pdo = $this->getTestDatabase();

        $this->table = new Table($pdo);
        $this->setProtectedProperty($this->table, 'table', 'comments');
    }
    public function testFind()
    {
        // $this->table->getPdo()->exec('INSERT INTO comments (name) VALUES ("a1");');
        // $this->table->getPdo()->exec('INSERT INTO comments (name) VALUES ("a2");');
        $this->makeInsertTestDatabase($this->table->getPdo(), "a1", "a2");
        $actual = $this->table->find(1);
        $this->assertInstanceOf(\stdClass::class, $actual);
        $this->assertEquals('a1', $actual->name);
    }

    public function testFindAsList()
    {
        $this->makeInsertTestDatabase($this->table->getPdo(), "a1", "a2");
        $actual = $this->table->findAsList();
        $this->assertEquals(['1' => 'a1', '2' => 'a2'], $actual);
    }

    public function testExists()
    {
        $this->makeInsertTestDatabase($this->table->getPdo(), "a1", "a2");
        $this->assertTrue($this->table->exists(1));
        $this->assertTrue($this->table->exists(2));
        $this->assertFalse($this->table->exists(3));
    }
}
