<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Database\fixtures;

use Sorani\SimpleFramework\Database\EntityInterface;

class Demo implements EntityInterface
{
    public int $id;
    public string $name;
    public string $content;
    public int $categoryId;
    public string $categoryName;
    public ?string $image;
    public string $created;
    public int $published;
    public string $createdAt;
    public string $updatedAt;

    /**
     * \Sorani\Database\Column(name=slug)
     * @var string
     */
    private $slug;

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug . 'demo';
    }
}
