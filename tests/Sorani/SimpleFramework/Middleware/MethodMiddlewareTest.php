<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Middleware\MethodMiddleware;
use Whoops\Handler\HandlerInterface;

class MethodMiddlewareTest extends TestCase
{
    /**
     * @var MethodMiddleware
     */
    private $middleware;

    public function setUp()
    {
        $this->middleware = new MethodMiddleware();
    }

    public function testAddMethodPsr15()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            // ->addMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
        ->method('handle')->with($this->callback(function (ServerRequestInterface $request) {
            return $request->getMethod() === 'DELETE';
        }));

        $request = (new ServerRequest('POST', '/demo'))
            ->withParsedBody(['_method' => 'DELETE']);
        $this->middleware->process($request, $handler);
    }
}
