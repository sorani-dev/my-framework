<?php

// declare(strict_types=1);

namespace Tests\App\Auth;

use App\Auth\ForbiddenMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Auth\ForbiddenException;
use Sorani\SimpleFramework\Session\ArraySession;
use Sorani\SimpleFramework\Session\SessionInterface;

class ForbiddenMiddlewareTest extends TestCase
{
    /**
     * @var SessionInterface
     */
    private $session;

    protected function setUp()
    {
        $this->session = new ArraySession();
    }

    private function makeRequest($path = '/')
    {
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn($path);
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request->method('getUri')->willReturn($uri);
        return $request;
    }

    public function makeHandler()
    {
        return $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
    }

    public function makeMiddleware()
    {
        return new ForbiddenMiddleware('/login', $this->session);
    }

    public function testCatchForbiddenException()
    {
        $handler = $this->makeHandler();
        $handler->expects($this->once())->method('handle')->willThrowException(new ForbiddenException());
        $response = $this->makeMiddleware()->process($this->makeRequest('/test'), $handler);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/login'], $response->getHeader('Location'));
        $this->assertEquals('/test', $this->session->get('auth.redirect'));
    }


    public function testBubbleError()
    {
        $handler = $this->makeHandler();
        $handler->expects($this->once())->method('handle')->willReturnCallback(function () {
            throw new \TypeError('test', 200);
        });
        try {
            $this->makeMiddleware()->process($this->makeRequest('/test'), $handler);
        } catch (\TypeError $e) {
            $this->assertEquals('test', $e->getMessage());
            $this->assertEquals(200, $e->getCode());
        }
    }

    public function testProcessValidRequest()
    {
        $handler = $this->makeHandler();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $handler->expects($this->once())->method('handle')->willReturn($response);

        $this->assertSame($response, $this->makeMiddleware()->process($this->makeRequest('/test'), $handler));
    }
}
