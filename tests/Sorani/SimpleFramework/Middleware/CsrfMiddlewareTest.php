<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Exception\CsrfInvalidException;
use Sorani\SimpleFramework\Middleware\CsrfMiddleware;

class CsrfMiddlewareTest extends TestCase
{
    /**
     * @var CsrfMiddleware
     */
    private $m;

    /**
     * @var \ArrayAccess
     */
    private $session;

    public function setUp()
    {
        $this->session = new \ArrayObject();
        $this->m = new CsrfMiddleware($this->session);
    }

    public function testLetGetRequestPass()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->once())->method('handle');

        $request = new ServerRequest('GET', '/demo');
        $this->m->process($request, $handler);
    }

    public function testBlockPostRequestWithoutCsrf()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->never())->method('handle');

        $request = new ServerRequest('POST', '/demo');
        $this->expectException(CsrfInvalidException::class);
        $this->m->process($request, $handler);
    }

    public function testLetPostWithTokenPass()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->once())->method('handle');

        $request = new ServerRequest('POST', '/demo');
        $token = $this->m->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->m->process($request, $handler);
    }

    public function testLetPostWithTokenPassOnce()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->once())->method('handle');

        $request = new ServerRequest('POST', '/demo');
        $token = $this->m->generateToken();
        $request = $request->withParsedBody(['_csrf' => $token]);
        $this->m->process($request, $handler);
        $this->expectException(CsrfInvalidException::class);
        $this->m->process($request, $handler);
    }


    public function testBlockPostRequestWithInvalidCsrf()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $handler->expects($this->never())->method('handle');

        $request = new ServerRequest('POST', '/demo');
        $request = $request->withParsedBody(['_csrf' => 'azezezertgyreghrtgb']);
        $this->expectException(CsrfInvalidException::class);
        $this->m->process($request, $handler);
    }

    public function testLimitTheNumberOfTokens()
    {
        $expectedSize = 50;
        for ($i = 0; $i < 110; $i++) {
            $token = $this->m->generateToken();
        }
        $this->assertCount($expectedSize, $this->session['csrf']);
        $this->assertEquals($token, $this->session['csrf'][49]);
    }
}
