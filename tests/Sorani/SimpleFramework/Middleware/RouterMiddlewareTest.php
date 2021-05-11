<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Middleware\RouterMiddleware;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Router\Route;

class RouterMiddlewareTest extends TestCase
{
    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    public function setUp(): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        $this->handler = $handler;
    }

    public function testPassParameters()
    {
        $route = new Route('demo', 'trim', ['id' => 2]);
        $middleware = $this->makeMiddleware($route);

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function (ServerRequestInterface $request) use ($route) {
                $this->assertEquals(2, $request->getAttribute('id'));
                $this->assertEquals($route, $request->getAttribute(get_class($route)));
                return new Response();
            }));
        $middleware->process(new ServerRequest('GET', '/demo'), $this->handler);
    }

    public function testCallNext()
    {
        $middleware = $this->makeMiddleware(null);
        $response = new Response();
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(function (ServerRequestInterface $request) use ($response) {
                return $response;
            }));

        $r = $middleware->process(new ServerRequest('GET', '/demo'), $this->handler);
        $this->assertEquals($response, $r);
    }

    private function makeMiddleware(?Route $route = null): RouterMiddleware
    {
        $router = $this->getMockBuilder(Router::class)->getMock();
        $router->method('match')->willReturn($route);
        return new RouterMiddleware($router);
    }
}
