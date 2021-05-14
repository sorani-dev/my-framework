<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Add methods to use with the Router
 *
 * Trait RouterAwareActionTrait
 * @package Sorani\Framework
 */
trait RouterAwareActionTrait
{

    /**
     * Send a Redirect Response
     *
     * @param  string $path
     * @param  array $params
     * @return ResponseInterface
     */
    public function redirect($path, array $params = [])
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
