<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoleMiddleware implements MiddlewareInterface
{
    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * @var string
     */
    private $role;

    /**
     * __construct
     *
     * @param  AuthInterface $auth
     * @param string $role
     */
    public function __construct(AuthInterface $auth, string $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->getUser();
        if (null === $user || !in_array($this->role, $user->getRoles())) {
            throw new ForbiddenException();
        }
        return $handler->handle($request);
    }
}
