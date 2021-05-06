<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use Sorani\SimpleFramework\Database\EntityInterface;

/**
 * Category Entity
 * Describes a Category (in the respective fields in the db table)
 */
class Category implements EntityInterface
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $slug;
}
