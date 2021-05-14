<?php

// declare(strict_types=1);

namespace App\Blog\Table;

use App\Blog\Entity\Category;
use Sorani\SimpleFramework\Database\Table;

class CategoryTable extends Table
{
    /**
     * {@inheritdoc}
     */
    protected $entity = Category::class;

    /**
     * {@inheritdoc}
     */
    protected $table = 'categories';
}
