<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @class App
 * Main App: Entry to Application
 */
class App
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];

    /**
     *Container
     * @var ContainerInterface
     */
    private $container;

    /**
     * App constructor
     *
     * @param  ContainerInterface $container
     * @param  string[] $modules List of modules to load
     * @param  array $dependencies
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules = $container->get($module);
        }
    }

    /**
     * Start the App
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $parsedBody = $request->getParsedBody();
        if (
            isset($parsedBody['_method']) &&
            in_array($parsedBody['_method'], ['PATCH', 'PUT', 'DELETE'])
        ) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        /** @var Router $router */
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (null === $route) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function (ServerRequestInterface $request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is neither a string nor an instance of ResponseInterface');
        }
    }

    /**
     * Get the Container
     *
     * @return  ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
