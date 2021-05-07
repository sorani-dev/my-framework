<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Router;

class RouterMiddleware
{
    /**
     * @var Router $router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        $route = $this->router->match($request);
        if (null === $route) {
            return $next($request);
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function (ServerRequestInterface $request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $request = $request->withAttribute(get_class($route), $route);
        return $next($request);
    }
}
