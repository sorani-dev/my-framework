<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Auth;

/**
 * User management
 */
interface UserInterface
{

    /**
     * Get the username of the User
     *
     * @return string
     */
    public function getUsername();

    /**
     * Get the User's Roles
     *
     * @return string[]
     */
    public function getRoles();
}
