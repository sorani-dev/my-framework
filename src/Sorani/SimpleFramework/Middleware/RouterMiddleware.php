<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Router;

/**
 * Router: match an url to a Route
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var Router $router
     */
    private $router;

    /**
     * Constructor
     *
     * @param  Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);
        if (null === $route) {
            return $handler->handle($request);
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function (ServerRequestInterface $request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $request = $request->withAttribute(get_class($route), $route);
        return $handler->handle($request);
    }
}
