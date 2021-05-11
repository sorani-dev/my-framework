<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Middleware\TrailingSlashMiddleware;
use Sorani\SimpleFramework\TestCase\MiddlewareTestCase;

class TrailingSlashMiddlewareTest extends MiddlewareTestCase
{
    public function testRedirectSlash()
    {
        $request = (new ServerRequest('GET', '/demo/'));
        $middleware = new TrailingSlashMiddleware();
        $this->handler
            ->expects($this->never())
            ->method('handle')
            ->will($this->returnCallback(function ($request) {
            }));

        $response = $middleware->process($request, $this->handler);
        $this->assertEquals(['/demo'], $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testCallNextIfNoSlash()
    {
        $request = (new ServerRequest('GET', '/demo'));
        $response = new Response();
        $middleware = new TrailingSlashMiddleware();
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(fn ($request) => $response));

        $this->assertEquals($response, $middleware->process($request, $this->handler));
    }
}
