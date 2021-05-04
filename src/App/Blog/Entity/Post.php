<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use DateTime;
use DateTimeImmutable;

/**
 * Post Entity
 * Describes a Post (in the respective fields in the db table)
 */
class Post
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

    /**
     * @var string
     */
    public string $content;

    /**
     * @var string|\DateTimeImmutable
     */
    public $created_at;

    /**
     * @var string|\DateTimeImmutable
     */
    public $updated_at;

    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new DateTimeImmutable($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new DateTimeImmutable($this->updated_at);
        }
    }
}
