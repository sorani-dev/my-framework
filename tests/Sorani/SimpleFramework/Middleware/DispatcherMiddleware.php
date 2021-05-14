<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use Mezzio\Router\Middleware\DispatchMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Middleware\DispatcherMiddleware as MiddlewareDispatcherMiddleware;
use Sorani\SimpleFramework\Middleware\NotFoundMiddleware;
use Sorani\SimpleFramework\Router\Route;

class DispatcherMiddleware extends TestCase
{
    public function testDispatchTheCallback()
    {
        $callback = function () {
            return 'Hello';
        };
        $route = new Route('demo', $callback, []);
        $request = (new ServerRequest('GET', '/demo'))->withAttribute(Route::class, $route);
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $dispatcher = new MiddlewareDispatcherMiddleware($container);
        $response = $dispatcher->process($request, $this->getMockBuilder(RequestHandlerInterface::class)->getMock());
        $this->assertEquals('Hello', $response->getBody());
    }

    public function testCallNextIfNotRoutes()
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        $handler->expects($this->once())->method('handle')->willReturn($response);

        $request = new ServerRequest('GET', '/demo');
        $dispatcher = new MiddlewareDispatcherMiddleware($container);
        $this->assertEquals($response, $dispatcher->process($request, $handler));
    }
}
