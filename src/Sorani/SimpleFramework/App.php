<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework;

use GuzzleHttp\Psr7\Response;
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

    private $dependencies = [];

    /**
     *
     * @var Router
     */
    private $router;

    /**
     * App constructor
     *
     * @param  string[] $modules List of modules to load
     * @param  array $dependencies
     */
    public function __construct(array $modules = [], ?array $dependencies = [])
    {
        $this->router = new Router();
        if (isset($dependencies['renderer'])) {
            $dependencies['renderer']->addGlobal('router', $this->router);
        }
        foreach ($modules as $module) {
            $this->modules = new $module($this->router, $dependencies['renderer']);
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
        if (!empty($uri) && $uri[-1] === '/') {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }
        $route = $this->router->match($request);
        if (null === $route) {
            return new Response(404, [], '<h1>Erreur 404</h1>');
        }
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function (ServerRequestInterface $request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);
        $response = call_user_func_array($route->getCallback(), [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('The response is neither a string nor an instance of ResponseInterface');
        }
    }
}
