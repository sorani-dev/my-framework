<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Session;

/**
 * Interface to manage session data
 */
interface SessionInterface
{
    /**
     * Get Session information based on its key with a default value if key not found (null if not defined)
     *
     * @param  string     $key
     * @param  mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Add session information as value to a key
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function set(string $key, $value): void;


    /**
     * Delete a key in the Session
     *
     * @param  string $key
     * @return void
     */
    public function delete(string $key): void;

    /**
     * Check if the key in the Session
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Destroys the current session
     *
     * @return void
     */
    public function destroy(): void;
}
