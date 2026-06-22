<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Auth\AuthInterface;
use Sorani\SimpleFramework\Auth\ForbiddenException;
use Sorani\SimpleFramework\Auth\UserInterface;
use Sorani\SimpleFramework\Middleware\LoggedInMiddleware;
use Sorani\SimpleFramework\TestCase\MiddlewareTestCase;

class LoggedInMiddlewareTest extends MiddlewareTestCase
{
    /**
     * Create a mock middleware
     * @param UserInterface|null $user
     * @return LoggedInMiddleware
     */
    public function makeMiddleware(?UserInterface $user): LoggedInMiddleware
    {
        $auth = $this->getMockBuilder(AuthInterface::class)->getMock();
        $auth->method('getUser')->willReturn($user);
        return new LoggedInMiddleware($auth);
    }

    /**
     * Create a mock request handler
     * @param InvokedCount $calls
     * @return RequestHandlerInterface
     */
    public function makeHandler(InvokedCount $calls)
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
