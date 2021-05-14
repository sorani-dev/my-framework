<?php

// declare(strict_types=1);

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use Sorani\SimpleFramework\Database\Table;
use Sorani\SimpleFramework\Database\Query\QueryBuilder;

class PostTable extends Table
{
    /**
     * {@inheritdoc}
     */
    protected $entity = Post::class;

    /**
     * {@inheritdoc}
     */
    protected $table = 'posts';

    /**
     * Find all Posts results linked with categories
     *
     * @return QueryBuilder
     */
    public function findAll()
    {
        $category = new CategoryTable($this->pdo);
        return $this->makeQuery()->fields('p.*, c.name AS category_name, c.slug AS category_slug')
            ->joinByString($category->getTable() . ' AS c', 'c.id = p.category_id', QueryBuilder::JOIN_LEFT)
            ->orderBy('p.created_at', QueryBuilder::ORDERBY_DESC);
    }

    /**
     * Find Public Posts
     *
     * @return QueryBuilder
     */
    public function findPublic()
    {
        return $this->findAll()->where('p.created_at < NOW()')
        ->where('published = 1');
    }

    /**
     * Find Category for the given Post
     *
     * @param  mixed $categoryId
     * @return QueryBuilder
     */
    public function findPublicForCategory($categoryId)
    {
        return $this->findPublic()->where('p.category_id = :category')->params([':category' => $categoryId]);
    }

    /**
     * Find Post by its id
     *
     * @param  int $postId
     * @return Post
     */
    public function findWithCategory($postId)
    {
        return $this->findPublic()->where('p.id = :id')->params([':id' => $postId])->fetch();
    }
}
