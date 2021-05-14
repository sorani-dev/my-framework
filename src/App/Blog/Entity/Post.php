<?php

// declare(strict_types=1);

namespace App\Blog\Entity;

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
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $image;


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
    public function setId($id)
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
    public function setName($name)
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
    public function setContent($content)
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
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * Get the thumbnail of the image
     *
     * @return string
     */
    public function getThumb()
    {
        $file = pathinfo($this->image);
        $filename = $file['filename'];
        $extension = $file['extension'];

        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }

    /**
     * Get the image URL for display
     *
     * @return string
     */
    public function getImageUrl()
    {
        return '/uploads/posts/' . $this->image;
    }
}
