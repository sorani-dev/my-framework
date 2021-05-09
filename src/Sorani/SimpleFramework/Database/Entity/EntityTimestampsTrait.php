<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Entity;

use DateTimeImmutable;

trait EntityTimestampsTrait
{
    /**
     * @var string|\DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @var string|\DateTimeImmutable
     */
    protected $updatedAt;

    /**
     * Get the value of createdAt
     *
     * @return  string|\DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  string|\DateTimeImmutable  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        if ($this->createdAt) {
            $this->createdAt = new DateTimeImmutable($this->createdAt);
        }


        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  string|\DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param  string|\DateTimeImmutable  $updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        if ($this->updatedAt) {
            $this->updatedAt = new DateTimeImmutable($this->updatedAt);
        }

        return $this;
    }
}
