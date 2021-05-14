<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Middleware\NotFoundMiddleware;

class NotFoundMiddlewareTest extends TestCase
{
    public function testSendNotFound()
    {
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        $handler->expects($this->never())->method('handle')->willReturn($response);
        $request = new ServerRequest('GET', '/demo');
        $middleware = new NotFoundMiddleware();
        /** @var ResponseInterface */
        $response = $middleware->process($request, $handler);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
