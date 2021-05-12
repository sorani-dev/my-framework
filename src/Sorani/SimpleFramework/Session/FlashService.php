<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Session;

/**
 * Manage Flash Messages
 */
class FlashService
{
    /**
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKey = '__FLASH__';

    /**
     *
     * @var string[]
     */
    private $messages;

    /**
     * FlashService Constructor
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Add a success message
     * @param string $message
     * @return void
     */
    public function success(string $message): void
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    /**
     * Set an error message
     * @param string $message
     */
    public function error(string $message): void
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    /**
     * Retrieve a message
     * @param  string $type
     * @return string|null
     */
    public function get(string $type): ?string
    {
        if (null === $this->messages) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }
        return $this->messages[$type] ?? null;
    }
}
