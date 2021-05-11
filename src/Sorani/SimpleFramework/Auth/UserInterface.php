<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Auth;

interface UserInterface
{

    /**
     * Get the username of the User
     *
     * @return string
     */
    public function getUsername(): string;

    /**
     * Get the User's Roles
     *
     * @return string[]
     */
    public function getRoles(): array;
}
