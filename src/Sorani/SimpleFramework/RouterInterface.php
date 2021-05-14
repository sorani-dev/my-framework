<?php

// declare(strict_types=1);

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
interface RouterInterface
{
    /**
     * GET Method
     *
     * @param  string $path Route path
     * @param  callable|string $callable
     * @param  string $name Route name
     * @return void
     */
    public function get($path, $callable, $name = null, array $options = []);

    /**
     * POST method
     *
     * @param  string $path
     * @param  callable|string $callable
     * @param  string|null $name Route name
     * @return void
     */
    public function post($path, $callable, $name = null, array $options = []);

    /**
     * DELETE method
     *
     * @param  string $path
     * @param  callable|string $callable
     * @param  string|null $name Route name
     * @return void
     */
    public function delete($path, $callable, $name = null, array $options = []);

    /**
     * Generates CRUD Routes
     *
     * @param  string $prefixPath
     * @param  string $callable
     * @param  string|null $prefixName
     * @return void
     */
    public function crud($prefixPath, $callable, $prefixName = null);

    /**
     * Match a request path to a route and returns a Route if it matches or null on failure
     *
     * @param  ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request);


    /**
     * Generate a URI from a Route name
     *
     * @param  string $name Route name
     * @param  array $params route parameters
     * @param  array $queryParams route query parameters
     * @return string generated URI
     */
    public function generateUri($name, array $params = [], array $queryParams = []);
}
