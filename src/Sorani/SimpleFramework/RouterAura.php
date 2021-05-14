<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework;

use AltoRouter;
use Aura\Router\RouterContainer;
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
class RouterAura implements RouterInterface
{
    /**
     * @var RouterContainer
     */
    private $router;

    /**
     * Constructor
     *
     * @param  string|null $cache Cache path or disable cache if null
     */
    public function __construct($cache = null)
    {
        $this->router = new RouterContainer();
        if ($cache !== null) {
            $this->loadRoutes($cache);
        }
    }

    /**
     * GET Method
     *
     * @param  string $path Route path
     * @param  callable|string $callable
     * @param  string $name Route name
     * @return void
     */
    public function get($path, $callable, $name = null, array $options = [])
    {
        $this->router->getMap()->get(
            $name,
            $path,
            new MiddlewareApp($callable, $options)
        )->tokens($options['params']);
    }

    /**
     * POST method
     *
     * @param  string $path
     * @param  callable|string $callable
     * @param  string|null $name Route name
     * @return void
     */
    public function post($path, $callable, $name = null, array $options = [])
    {
        $this->router->getMap()->post(
            $name,
            $path,
            new MiddlewareApp($callable, $options)
        )->tokens($options['params']);
    }

    /**
     * DELETE method
     *
     * @param  string $path
     * @param  callable|string $callable
     * @param  string|null $name Route name
     * @return void
     */
    public function delete($path, $callable, $name = null, array $options = [])
    {
        $this->router->getMap()->delete(
            $name,
            $path,
            new MiddlewareApp($callable, $options)
        )->tokens($options['params']);
    }


    /**
     * Generates CRUD Routes
     *
     * @param  string $prefixPath
     * @param  string $callable
     * @param  string|null $prefixName
     * @return void
     */
    public function crud($prefixPath, $callable, $prefixName = null)
    {
        $this->get($prefixPath, $callable, $prefixName . '.index');
        $this->get($prefixPath . '/new', $callable, $prefixName . '.create');
        $this->post($prefixPath . '/new', $callable);
        $this->get($prefixPath .  '/{id}', $callable, $prefixName . '.edit', ['params' => ['id' => '\d+']]);
        $this->post($prefixPath . '/{id}', $callable, ['params' => ['id' => '\d+']]);
        $this->delete($prefixPath . '/{id}', $callable, $prefixName . '.delete', ['params' => ['id' => '\d+']]);
    }

    /**
     * Match a request path to a route and returns a Route if it matches or null on failure
     *
     * @param  ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request)
    {
        $route = $this->router->getMatcher()->match($request);
        if ($route !== false) {
            return new Route(
                $route->name,
                $route->handler->getCallback(),
                $route->attributes
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
    public function generateUri($name, array $params = [], array $queryParams = [])
    {
        $uri = $this->router->getGenerator()->generate($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }

    public function saveRoutes($pathToCache)
    {
        $routes = $this->router->getMap();
        file_put_contents($pathToCache, serialize($routes));
    }

    public function loadRoutes($pathToCache)
    {
        $routes = unserialize(file_get_contents($pathToCache));
        $this->router->getMap()->setRoutes($routes);
    }
}
