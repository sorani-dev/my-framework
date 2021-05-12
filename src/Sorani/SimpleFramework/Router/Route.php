<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Router;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Route
 * Details a potential matching route
 */
class Route
{
    /**
     * @var string Route name
     */
    private string $name;

    /**
     * @var callable|MiddlewareInterface
     */
    private $callback;

    /**
     * @var array|null Route parameters
     */
    private ?array $parameters;

    /**
     * @var array
     */
    private array $options = [];


    /**
     * Route constructor
     *
     * @param  string $name Route name
     * @param  callable|string $callback what to call when route found
     * @param  array $parameters Parameters to inject in route
     * @param  array $options Route options
     */
    public function __construct(string $name, $callback, array $parameters, ?array $options = [])
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
        $this->options = $options;
    }

    /**
     * Get the route name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the route callback
     * @return callable|string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get the Route URL parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }

    /**
     * Get the Route options
     *
     * @return  array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
