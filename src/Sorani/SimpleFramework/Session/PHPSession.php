<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Session;

use ArrayAccess;
use Sorani\Session\Exceptions\SessionException;

/**
 * {@inheritdoc}
 */
class PHPSession implements SessionInterface, ArrayAccess
{
    /**
     * Get Session information based on its key with a default value if key not found (null if not defined)
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Add session information as value to a key
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->ensureStarted();
        $_SESSION[$key] = $value;
    }


    /**
     * Delete a key in the Session
     *
     * @param  string $key
     * @return mixed
     */
    public function delete($key)
    {
        $this->ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Check if the key in the Session
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        $this->ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Destroys the current session
     *
     * @return void
     */
    public function destroy()
    {
        $this->ensureStarted();
        $_SESSION = [];
    }

    /**
     * Start a session if SESSION_NONE, throws a SessionException if SESSION_DISABLED, does nothing if SESSION_ACTIVE
     * @throws \Exception
     */
    protected function ensureStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } elseif (php_sapi_name() === 'cli') {
            $_SESSION = [];
        } elseif (session_status() === PHP_SESSION_DISABLED) {
            throw new SessionException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
