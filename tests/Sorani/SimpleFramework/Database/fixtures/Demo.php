<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Database\fixtures;

use Sorani\SimpleFramework\Database\EntityInterface;

class Demo implements EntityInterface
{
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
