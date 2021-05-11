<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Middleware\RoutePrefixedMiddleware;
use Sorani\SimpleFramework\Modules\Module;

/**
 * @class App
 * Main App: Entry to Application
 */
class App implements RequestHandlerInterface
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];


    /**
     * Definition file containing a configuration array
     * @var string
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;

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
     * App Constructor
     *
     * @param  string $definition definition file path
     */
    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Add a Module to the Application (functionality)
     *
     * @param  string $module
     * @return self
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }


    /**
     * Add a middleware (comportement)
     *
     * @param  mixed $routePrefix
     * @param  mixed $middleware
     * @return self
     */
    public function pipe(string $routePrefix, ?string $middleware = null): self
    {
        if (null === $middleware) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }


    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (null === $middleware) {
            throw new \Exception('No middleware has been able to intercept the request');
        } elseif (is_callable($middleware)) {
            return $middleware($request, [$this, 'process']);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->process($request);
    }

    /**
     * Start the App
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
        /*


        /** @var Router $router * /
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
*/
    }

    /**
     * Get the Container
     *
     * @return  ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();

            $env = getenv('ENV') ?:  'production';
            if ($env === 'production') {
                $apcuAvailable = function_exists('apcu_enabled') && apcu_enabled();
                if ($apcuAvailable) {
                    $builder->enableDefinitionCache(__NAMESPACE__);
                }
                $builder->enableCompilation('tmp/proxies');
                $builder->writeProxiesToFile(true, __DIR__ . '/tmp/proxies');
                // $builder->enableDefinitionCache(new ApcuCache());
                // $builder->writeProxiesToFile(true, __DIR__ . '/tmp/proxies');
            }

            $builder->addDefinitions($this->definition);

            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * getMiddleware
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

    /**
     * Get list of modules
     *
     * @return  Module[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
