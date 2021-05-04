<?php

declare(strict_types=1);

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Sorani\SimpleFramework\Database\PaginatedQuery;
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
     * @param  int $perPage number of results to display per page
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            // 'SELECT * FROM posts ORDER BY created_at DESC LIMIT 10;'
            'SELECT * FROM posts ORDER BY created_at DESC',
            'SELECT COUNT(*) FROM posts',
            Post::class
        );
        return  (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Find a single Post by its ID or null if not found
     *
     * @param  int $id
     * @return Post|null
     */
    public function find(int $id): ?Post
    {
        $statement = $this->pdo
            ->prepare('SELECT * FROM posts WHERE posts.id = ?;');
        $statement->execute([$id]);
        $statement->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $post = $statement->fetch() ?: null;
    }

    /**
     * Update a record in the database
     *
     * @param  int $id
     * @param  array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldsQuery = join(' , ', array_map(function ($field) {
            return "{$field} = :{$field}";
        }, array_flip($params)));
        $statement = $this->pdo->prepare("UPDATE posts SET {$fieldsQuery} WHERE id = :id;");
        $params[':id'] = $id;
        return $statement->execute($params);
    }

    /**
     * Create a new record in the database
     *
     * @param  array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $names = join(', ', $fields);
        $values = join(', :', $fields);
        $statement = $this->pdo->prepare("INSERT INTO posts ({$names}) VALUES (:{$values});");
        return $statement->execute($params);
    }

    /**
     * Delete a record from the database
     *
     * @param  int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM posts WHERE id = :id;");
        return $statement->execute([':id' => $id]);
    }
}
