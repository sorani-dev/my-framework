<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework;

use Fig\Http\Message\RequestMethodInterface;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Route as RouterRoute;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Router\MiddlewareApp;
use Sorani\SimpleFramework\Router\Route;

/**
 * Router
 * Register and match Routes
 */
class Router
{
    /**
     * @var FastRouteRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * get
     *
     * @param  string $path
     * @param  callable $callable
     * @param  string $name
     * @return void
     */
    public function get(string $path, callable $callable, string $name = null, ?array $options = [])
    {
        $this->router->addRoute(
            new RouterRoute(
                $path,
                new MiddlewareApp($callable, $options),
                [RequestMethodInterface::METHOD_GET],
                $name
            )
        );
    }
    /**
     * get
     *
     * @param  string $path
     * @param  callable $callable
     * @param  string $name
     * @return void
     */
    public function post(string $path, callable $callable, string $name = null, ?array $options = [])
    {
        $this->router->addRoute(
            new RouterRoute(
                $path,
                new MiddlewareApp($callable, $options),
                [RequestMethodInterface::METHOD_POST],
                $name
            )
        );
    }

    /**
     * Match a request path to a route and returns a Route if it matches or null on failure
     *
     * @param  ServerRequestInterface $request
     * @return Route\null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $route = $this->router->match($request);
        if ($route->isSuccess()) {
            return new Route(
                $route->getMatchedRouteName(),
                $route->getMatchedRoute()->getMiddleware()->getCallback(),
                $route->getMatchedParams()
            );
        }
        return null;
    }

    public function generateUri(string $name, array $params = []): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}
