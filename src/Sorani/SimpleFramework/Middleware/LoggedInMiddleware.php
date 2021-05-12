<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Auth\AuthInterface;
use Sorani\SimpleFramework\Auth\ForbiddenException;

/**
 * check if user is logged in
 */
class LoggedInMiddleware implements MiddlewareInterface
{

    /**
     * @var AuthInterface
     */
    private $auth;

    /**
     * Constructor
     *
     * @param AuthInterface $auth
     */
    public function __construct(AuthInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->auth->getUser();
        if (null === $user) {
            throw new ForbiddenException();
        }
        return $handler->handle($request->withAttribute('user', $user));
    }
}
