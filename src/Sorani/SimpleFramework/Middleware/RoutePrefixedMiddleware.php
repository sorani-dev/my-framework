<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * used for prefixed Routes
 */
class RoutePrefixedMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var string
     */
    private $middleware;

    /**
     * RoutePrefixedMiddleware Constructor
     *
     * @param  ContainerInterface $container
     * @param  string $routePrefix
     * @param  string $middleware
     */
    public function __construct(ContainerInterface $container, string $routePrefix, string $middleware)
    {
        $this->container = $container;
        $this->routePrefix = $routePrefix;
        $this->middleware = $middleware;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (0 === strpos($path, $this->routePrefix)) {
            return $this->container->get($this->middleware)->process($request, $handler);
        }
        return $handler->handle($request);
    }
}
