<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Table\UserTable;
use Sorani\SimpleFramework\Auth\AuthInterface;
use Sorani\SimpleFramework\Auth\UserInterface;
use Sorani\SimpleFramework\Database\Exception\NoRecordFoundException;
use Sorani\SimpleFramework\Session\SessionInterface;

/**
 * {@inheritdoc}
 */
class DatabaseAuth implements AuthInterface
{
    /**
     * @var UserTable
     */
    private $userTable;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var User
     */
    private $user;

    /**
     * DatabaseAuth Constructor
     *
     * @param  UserTable $userTable
     * @param  SessionInterface $session
     */
    public function __construct(UserTable $userTable, SessionInterface $session)
    {
        $this->userTable = $userTable;
        $this->session = $session;
    }

    /**
     * Check Username and Password for correct info in database
     *
     * @param  string $username
     * @param  string $password
     * @return User|null
     */
    public function login(string $username, string $password): ?User
    {
        if (empty($username) || empty($password)) {
            return null;
        }

        /** @var User $user */
        $user = $this->userTable->findBy('username', $username);
        if ($user && password_verify($password, $user->password)) {
            $this->session->set('auth.user', $user->id);
            return $user;
        }

        return null;
    }

    /**
     * log out a User
     *
     * @return void
     */
    public function logout(): void
    {
        $this->session->delete('auth.user');
    }

    /**
     * {@inheritdoc}
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (null !== $this->user) {
            return $this->user;
        }

        $userId = $this->session->get('auth.user');
        if ($userId) {
            try {
                $this->user = $this->userTable->find((int)$userId);
                return $this->user;
            } catch (NoRecordFoundException $e) {
                $this->session->delete('auth.user');
                return null;
            }
        }
        return null;
    }
}
