<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use DateTimeImmutable;
use Sorani\SimpleFramework\Database\Entity\EntityTimestampsTrait;
use Sorani\SimpleFramework\Database\EntityInterface;

/**
 * Post Entity
 * Describes a Post (in the respective fields in the db table)
 */
class Post implements EntityInterface
{
    use EntityTimestampsTrait;

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
     * @var string
     */
    public $categoryName;


    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  int  $id
     *
     * @return  self
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of content
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * Get the value of categoryName
     *
     * @return  string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Set the value of categoryName
     *
     * @param  string  $categoryName
     *
     * @return  self
     */
    public function setCategoryName(string $categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }
}
