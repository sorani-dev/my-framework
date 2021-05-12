<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Middlewares\Utils\RequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\HandlerInterface;

class CombinedMiddlewareHandler implements RequestHandlerInterface
{
    /**
     * @var string[]
     */
    private $middlewares = [];

    /**
     * Middleware index (where in the queue are we?)
     * @var int
     */
    private $middlewareIndex = 0;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestHandler
     */
    private $handler;


    /**
     * Constructor
     *
     * @param  ContainerInterface $container
     * @param  array $middlewares
     * @param  RequestHandlerInterface $handler
     */
    public function __construct(ContainerInterface $container, array $middlewares, RequestHandlerInterface $handler)
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (null === $middleware) {
            return $this->handler->handle($request);
        } elseif (is_callable($middleware)) {
            $response = $middleware($request, [$this, 'handle']);
            if (is_string($response)) {
                return new Response(200, [], $response);
            }
            return $response;
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    /**
     * Get the current middleware
     *
     * @return object|null
     */
    private function getMiddleware()
    {
        if (isset($this->middlewares[$this->middlewareIndex])) {
            if (is_string($this->middlewares[$this->middlewareIndex])) {
                $middleware = $this->container->get($this->middlewares[$this->middlewareIndex]);
            } else {
                $middleware = $this->middlewares[$this->middlewareIndex];
            }
            $this->middlewareIndex++;
            return $middleware;
        }
        return null;
    }
}
