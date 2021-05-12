<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Entity;

use DateTimeImmutable;

/**
 * Defines a trait to manage Timestamps (created date, updated date)
 * Uses DateTimeImmutable in implementing
 * classes using the trait can override this trait with other classes implementing \DateTimeInterface
 */
trait EntityTimestampsTrait
{
    /**
     * @var \DateTimeInterface Created date as \DateTimeImmutable by trait implementation
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface Update date as \DateTimeImmutable by trait implementation
     */
    protected $updatedAt;

    /**
     * Get the value of createdAt
     *
     * @return  \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  string|\DateTimeInterface  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt): self
    {
        if (is_string($createdAt)) {
            $this->createdAt = new DateTimeImmutable($createdAt);
        }
        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return  \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param string\DateTimeInterface  $updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt): self
    {
        if (is_string($updatedAt)) {
            $this->updatedAt = new DateTimeImmutable($updatedAt);
        }

        return $this;
    }
}
