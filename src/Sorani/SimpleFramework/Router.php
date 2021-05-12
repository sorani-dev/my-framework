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

    /**
     * Constructor
     *
     * @param  string|null $cache Cache path or disable cache if null
     */
    public function __construct(?string $cache = null)
    {
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => null !== $cache,
            FastRouteRouter::CONFIG_CACHE_FILE => $cache,
        ]);
    }

    /**
     * GET Method
     *
     * @param  string $path Route path
     * @param  callable|string $callable
     * @param  string $name Route name
     * @return void
     */
    public function get(string $path, $callable, ?string $name = null, ?array $options = [])
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
     * POST method
     *
     * @param  string $path
     * @param  callable|string $callable
     * @param  string|null $name Route name
     * @return void
     */
    public function post(string $path, $callable, ?string $name = null, ?array $options = [])
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
     * DELETE method
     *
     * @param  string $path
     * @param  callable|string $callable
     * @param  string|null $name Route name
     * @return void
     */
    public function delete(string $path, $callable, ?string $name = null, ?array $options = [])
    {
        $this->router->addRoute(
            new RouterRoute(
                $path,
                new MiddlewareApp($callable, $options),
                [RequestMethodInterface::METHOD_DELETE],
                $name
            )
        );
    }


    /**
     * Generates CRUD Routes
     *
     * @param  string $prefixPath
     * @param  string $callable
     * @param  string|null $prefixName
     * @return void
     */
    public function crud(string $prefixPath, $callable, ?string $prefixName = null)
    {
        $this->get($prefixPath, $callable, $prefixName . '.index');
        $this->get($prefixPath . '/new', $callable, $prefixName . '.create');
        $this->post($prefixPath . '/new', $callable);
        $this->get($prefixPath .  '/{id:\d+}', $callable, $prefixName . '.edit');
        $this->post($prefixPath . '/{id:\d+}', $callable);
        $this->delete($prefixPath . '/{id:\d+}', $callable, $prefixName . '.delete');
    }

    /**
     * Match a request path to a route and returns a Route if it matches or null on failure
     *
     * @param  ServerRequestInterface $request
     * @return Route|null
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

    /**
     * Generate a URI from a Route name
     *
     * @param  string $name Route name
     * @param  array $params route parameters
     * @param  array $queryParams route query parameters
     * @return string generated URI
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
