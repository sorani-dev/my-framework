<?php

declare(strict_types=1);

namespace App\Blog\Table;

use stdClass;

class PostTable
{
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Paginate the Posts
     *
     * @return \stdClass[]
     */
    public function findPaginated(): array
    {
        return $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10;')
            ->fetchAll();
    }

    /**
     * Find a single Post
     *
     * @param  int $id
     * @return \stdClass
     */
    public function find(int $id): stdClass
    {
        $statement = $this->pdo
            ->prepare('SELECT * FROM posts WHERE posts.id = ?;');
        $statement->execute([$id]);
        return $post = $statement->fetch();
    }
}
