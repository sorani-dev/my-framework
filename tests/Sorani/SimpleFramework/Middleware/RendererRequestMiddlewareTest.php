<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Middleware\RendererRequestMiddleware;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class RendererRequestMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    /**
     * ]@var RendererInterface
     */
    private $renderer;

    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * @var RendererRequestMiddleware
     */
    private $m;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->handler->handle(Argument::type(ServerRequestInterface::class))
        ->willReturn(new Response());
        $this->handler = $this->handler->reveal();
        $this->m = new RendererRequestMiddleware($this->renderer->reveal());
    }
    public function testAddGlobalDomain()
    {
        $this->renderer->addGlobal('domain', 'http://grafikart.fr')->shouldBeCalled();
        $this->renderer->addGlobal('domain', 'http://localhost:3000')->shouldBeCalled();
        $this->renderer->addGlobal('domain', 'http://localhost')->shouldBeCalled();
        $this->m->process(new ServerRequest('GET', 'http://grafikart.fr/blog/demo'), $this->handler);
        $this->m->process(new ServerRequest('GET', 'http://localhost:3000/blog/demo'), $this->handler);
        $this->m->process(new ServerRequest('GET', 'http://localhost/blog/demo'), $this->handler);
    }
}
