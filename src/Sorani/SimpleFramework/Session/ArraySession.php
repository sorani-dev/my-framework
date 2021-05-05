<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Session;

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
    public function get(string $key, $default = null)
    {
        return $this->session[$key] ?? $default;
    }

    /**
     * Add session information as value to a key
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }


    /**
     * Delete a key in the Session
     *
     * @param  string $key
     * @return mixed
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }

    /**
     * Check if the key in the Session
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->session[$key]);
    }

    /**
     * Destroys the current session
     *
     * @return void
     */
    public function destroy(): void
    {
        $this->session = [];
    }
}
