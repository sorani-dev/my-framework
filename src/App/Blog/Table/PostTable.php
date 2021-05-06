<?php

declare(strict_types=1);

namespace App\Blog\Table;

use App\Blog\Entity\Post;
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
}
