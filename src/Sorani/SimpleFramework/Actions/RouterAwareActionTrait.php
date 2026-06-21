<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Sorani\SimpleFramework\Router;

/**
 * Add methods to use with the Router
 *
 * Trait RouterAwareActionTrait
 * @package Sorani\Framework
 */
trait RouterAwareActionTrait
{
    /**
     * @var Router
     */
    protected Router $router;

    /**
     * Send a Redirect Response
     *
     * @param  string $path
     * @param  array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
