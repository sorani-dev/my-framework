<?php

declare(strict_types=1);

namespace App\Blog\Table;

use App\Blog\Entity\Category;
use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Sorani\SimpleFramework\Database\PaginatedQuery;
use Sorani\SimpleFramework\Database\Table;

class PostTable extends Table
{
    protected $entity = Post::class;

    protected $table = 'posts';

    protected function paginationQuery(): string
    {
        return 'SELECT p.id, p.name, c.name AS category_name 
            FROM ' . $this->table . ' p 
            LEFT JOIN categories AS c ON c.id = p.category_id 
            ORDER BY created_at DESC';
    }

    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM posts AS p 
            LEFT JOIN categories AS c ON c.id = p.category_id
            ORDER BY p.created_at DESC",
            "SELECT COUNT(*) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM posts AS p 
            LEFT JOIN categories AS c ON c.id = p.category_id
            WHERE c.id=:category
            ORDER BY p.created_at DESC",
            "SELECT COUNT(*) FROM {$this->table} WHERE category_id = :category",
            $this->entity,
            [':category' => $categoryId]
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id): Post
    {
        return $this->fetchOrFail(
            '
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM posts AS p
            LEFT JOIN categories AS c ON c.id = p.category_id
            WHERE p.id = ?;',
            [$id]
        );
    }
}
