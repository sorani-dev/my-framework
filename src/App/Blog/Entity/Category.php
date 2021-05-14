<?php

// declare(strict_types=1);

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
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $slug;
}
