<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Router\Route;

class DispatcherMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        if (null === $route) { // no route match
            return $handler->handle($request);
        }
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        if (!is_array($callback)) {
            $callback = [$callback];
        }
        // $response = call_user_func_array($callback, [$request]);
        // if (is_string($response)) {
        //     return new Response(200, [], $response);
        // } elseif ($response instanceof ResponseInterface) {
        //     return $response;
        // } else {
        //     throw new \Exception('The response is neither a string nor an instance of ResponseInterface');
        // }
        return (new CombinedMiddleware($this->container, $callback))->process($request, $handler);
    }
}
