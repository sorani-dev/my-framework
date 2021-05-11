<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Auth;

interface AuthInterface
{

    /**
     * getUser
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;
}
