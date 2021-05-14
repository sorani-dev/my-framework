<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Middleware;

use App\Auth\User;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
// use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Auth\AuthInterface;
use Sorani\SimpleFramework\Auth\ForbiddenException;
use Sorani\SimpleFramework\Auth\RoleMiddleware;
use Sorani\SimpleFramework\Exception\CsrfInvalidException;
use Sorani\SimpleFramework\Middleware\CsrfMiddleware;

class RoleMiddlewareTest extends TestCase
{
    // use ProphecyTrait;

    /**
     * @var RoleMiddlewareTest
     */
    private $m;

    /**
     * @var AuthInterface
     */
    private $auth;

    public function setUp()
    {
        $this->auth = $this->prophesize(AuthInterface::class);
        $this->m = new RoleMiddleware(
            $this->auth->reveal(),
            'admin'
        );
    }

    public function testWithUnauthenticatedUser()
    {
        $this->auth->getUser()->willReturn(null);
        $this->expectException(ForbiddenException::class);
        $this->m->process(new ServerRequest('GET', '/demo'), $this->makeHandler()->reveal());
    }

    public function testWithBadRole()
    {
        $user = $this->prophesize(User::class);
        $user->getRoles()->willReturn(['user']);
        $this->auth->getUser()->willReturn($user->reveal());
        $this->expectException(ForbiddenException::class);
        $this->m->process(new ServerRequest('GET', '/demo'), $this->makeHandler()->reveal());
    }

    public function testWithGoodRole()
    {
        $user = $this->prophesize(User::class);
        $user->getRoles()->willReturn(['admin']);
        $this->auth->getUser()->willReturn($user->reveal());
        $actual = $this->m->process(new ServerRequest('GET', '/demo'), $this->makeHandler()->reveal());
        $this->assertInstanceOf(ResponseInterface::class, $actual);
    }

    private function makeHandler()
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle(Argument::any())->willReturn(new Response());
        return $handler;
    }
}
