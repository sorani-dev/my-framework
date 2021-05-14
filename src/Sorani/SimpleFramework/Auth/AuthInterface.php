<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Auth;

/**
 * Interface to manage User Authentication
 */
interface AuthInterface
{
    /**
     * Get a User if present else return null
     *
     * @return UserInterface|null
     */
    public function getUser();
}
