<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Database\Entity;

/**
 * Defines an Interface for managing Timestamps (created date, updated date)
 */
interface EntityTimestampsInterface
{
    /**
     * Get the value of createdAt
     *
     * @return  \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * Set the value of createdAt
     *
     * @param  string|\DateTimeInterface  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt);

    /**
     * Get the value of updatedAt
     *
     * @return  \DateTimeInterface
     */
    public function getUpdatedAt();

    /**
     * Set the value of updatedAt
     *
     * @param string\DateTimeInterface  $updatedAt
     *
     * @return  self
     */
    public function setUpdatedAt($updatedAt);
}
