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
     * @var string
     */
    private string $name;

    /**
     * @var callable|MiddlewareInterface
     */
    private $callback;

    /**
     * @var array
     */
    private ?array $parameters;

    /**
     * @var array
     */
    private array $options = [];


    public function __construct(string $name, $callback, array $parameters, ?array $options = [])
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->parameters = $parameters;
        $this->options = $options;
    }

    /**
     * Get the value of name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of callback
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Get the URL of parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }

    /**
     * Get the options
     *
     * @return  array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
