<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Router;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @class MiddlewareApp
 * Use this class to make callables compatible with mezzio router interface
 * which needs the MiddlewareInterface
 */
class MiddlewareApp implements MiddlewareInterface
{

    /**
     * @var string|callable
     */
    private $callback;

    /**
     * @var array
     */
    private $extraOptions;

    /**
     * tMiddlewareApp constructor.
     *
     * @param  callable|string $callback
     * @param  mixed $extraOptions Extra options to add to the Route
     * @return void
     */
    public function __construct($callback, ?array $extraOptions = [])
    {
        $this->callback = $callback;
        $this->extraOptions = $extraOptions;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface|null $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler = null): ResponseInterface
    {
        return $handler->handle($request);
    }

    /**
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get Route's extraOptions
     *
     * @return  array
     */
    public function getExtraOptions(): array
    {
        return $this->extraOptions;
    }
}
