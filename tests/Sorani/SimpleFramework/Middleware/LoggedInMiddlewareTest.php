<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Auth\AuthInterface;
use Sorani\SimpleFramework\Auth\ForbiddenException;
use Sorani\SimpleFramework\Auth\UserInterface;
use Sorani\SimpleFramework\Middleware\LoggedInMiddleware;
use Sorani\SimpleFramework\TestCase\MiddlewareTestCase;

class LoggedInMiddlewareTest extends MiddlewareTestCase
{
    public function makeMiddleware($user)
    {
        $auth = $this->getMockBuilder(AuthInterface::class)->getMock();
        $auth->method('getUser')->willReturn($user);
        return new LoggedInMiddleware($auth);
    }

    public function makeHandler($calls)
    {
        $this->handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->handler->expects($calls)->method('handle')->willReturn($response);
        return $this->handler;
    }

    public function testThrowIfNoUser()
    {
        $request = new ServerRequest('GET', '/demo/');
        $this->expectException(ForbiddenException::class);
        $this->makeMiddleware(null)
            ->process($request, $this->makeHandler($this->never()));
    }

    public function testNextIfUser()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $request = new ServerRequest('GET', '/demo/');
        $this->makeMiddleware($user)
        ->process($request, $this->makeHandler($this->once()));
    }
}
