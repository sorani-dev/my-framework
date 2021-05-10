<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Entity;

use DateTimeImmutable;

trait EntityTimestampsTrait
{
    /**
     * @var \DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    protected $updatedAt;

    /**
     * Get the value of createdAt
     *
     * @return  \DateTimeImmutable
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
        if (is_string($createdAt)) {
            $this->createdAt = new DateTimeImmutable($createdAt);
        }


        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  \DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param string\DateTime  $updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt)
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new DateTimeImmutable($updatedAt);
        }

        return $this;
    }
}
