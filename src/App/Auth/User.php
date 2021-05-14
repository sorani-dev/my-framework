<?php

// declare(strict_types=1);

namespace App\Auth;

use Sorani\SimpleFramework\Auth\UserInterface;
use Sorani\SimpleFramework\Database\EntityInterface;

class User implements EntityInterface, UserInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string[]
     */
    public $roles;

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return [];
    }
}
