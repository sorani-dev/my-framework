<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework;

use AltoRouter;
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
class Router implements RouterInterface
{
    /**
     * @var AltoRouter
     */
    private $router;

    /**
     * Constructor
     *
     * @param  string|null $cache Cache path or disable cache if null
     */
    public function __construct($cache = null)
    {
        $this->router = new AltoRouter();
        $this->router->addMatchTypes(['cId' => '[a-zA-Z]{2}[0-9](?:_[0-9]++)?', 'slug' => '[a-z0-9\-]+']);
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
        $this->router->map(
            RequestMethodInterface::METHOD_GET,
            $path,
            $callable,
            $name
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
    public function post($path, $callable, $name = null, array $options = [])
    {
        $this->router->map(
            RequestMethodInterface::METHOD_POST,
            $path,
            $callable,
            $name
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
    public function delete($path, $callable, $name = null, array $options = [])
    {
        $this->router->map(
            RequestMethodInterface::METHOD_DELETE,
            $path,
            $callable,
            $name
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
    public function crud($prefixPath, $callable, $prefixName = null)
    {
        $this->get($prefixPath, $callable, $prefixName . '.index');
        $this->get($prefixPath . '/new', $callable, $prefixName . '.create');
        $this->post($prefixPath . '/new', $callable);
        $this->get($prefixPath .  '/[i:id]', $callable, $prefixName . '.edit');
        $this->post($prefixPath . '/[i:id]', $callable);
        $this->delete($prefixPath . '/[i:id]', $callable, $prefixName . '.delete');
    }

    /**
     * Match a request path to a route and returns a Route if it matches or null on failure
     *
     * @param  ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request)
    {
        $route = $this->router->match();
        // $route = $this->router->match((string)$request->getUri(), $request->getMethod());
        if (is_array($route)) {
            return new Route(
                $route['name'],
                $route['target'],
                is_array($route['params']) ? $route['params'] : []
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
        $uri = $this->router->generate($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
