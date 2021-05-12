<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Checks the User has the right role
 */
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
     * Role Constructor
     *
     * @param  AuthInterface $auth
     * @param string $role User role
     */
    public function __construct(AuthInterface $auth, string $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->getUser();
        if (null === $user || !in_array($this->role, $user->getRoles())) {
            throw new ForbiddenException();
        }
        return $handler->handle($request);
    }
}
