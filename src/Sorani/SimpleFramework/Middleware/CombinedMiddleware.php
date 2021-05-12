<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $middlewares;

    /**
     * Constructor
     *
     * @param  ContainerInterface $container
     * @param  array $middlewares
     */
    public function __construct(ContainerInterface $container, array $middlewares)
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handler = new CombinedMiddlewareHandler($this->container, $this->middlewares, $handler);
        return $handler->handle($request);
    }
}
