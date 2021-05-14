<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Session;

/**
 * {@inheritdoc}
 */
class ArraySession implements SessionInterface
{
    /**
     * @var array
     */
    private $session = [];

    /**
     * Get Session information based on its key with a default value if key not found (null if not defined)
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->session[$key]) ? $this->session[$key] : $default;
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
        $this->session[$key] = $value;
    }


    /**
     * Delete a key in the Session
     *
     * @param  string $key
     * @return mixed
     */
    public function delete($key)
    {
        unset($this->session[$key]);
    }

    /**
     * Check if the key in the Session
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->session[$key]);
    }

    /**
     * Destroys the current session
     *
     * @return void
     */
    public function destroy()
    {
        $this->session = [];
    }
}
