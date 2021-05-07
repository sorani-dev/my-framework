<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Router\Route;

class DispatcherMiddleware
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Route::class);
        if (null === $route) { // no route match
            return $next($request);
        }
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is neither a string nor an instance of ResponseInterface');
        }
        return $next($request);
    }
}
