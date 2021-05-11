<?php

declare(strict_types=1);

namespace App\Auth;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Auth\ForbiddenException;
use Sorani\SimpleFramework\Auth\UserInterface;
use Sorani\SimpleFramework\Response\RedirectResponse;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Session\SessionInterface;

class ForbiddenMiddleware implements MiddlewareInterface
{
    /**
     * @var string
     */
    private $loginPath;

    /**
     * @var SessionInterface
     */
    private $sessionInterface;

    public function __construct(string $loginPath, SessionInterface $sessionInterface)
    {
        $this->loginPath = $loginPath;
        $this->sessionInterface = $sessionInterface;
    }

    /**
     * process
     *
     * @param  mixed $request
     * @param  mixed $handler
     * @return ResponseInterface
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException $e) {
            return $this->redirectLogin($request);
        } catch (\TypeError $e) {
            if (false !== strpos($e->getMessage(), UserInterface::class)) {
                return $this->redirectLogin($request);
            }
            throw $e;
        }
    }

    public function redirectLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->sessionInterface->set('auth.redirect', $request->getUri()->getPath());
        // $this->flashService->error('Vous devez posséder un compte pour accéder à cette page');
        (new FlashService($this->sessionInterface))->error('You must have an account to access this page');
        return new RedirectResponse($this->loginPath);
        $this->sessionInterface->set('auth.redirect', $request->getUri()->getPath());
        // $this->flashService->error('Vous devez posséder un compte pour accéder à cette page');
        (new FlashService($this->sessionInterface))->error('You must have an account to access this page');
        return new RedirectResponse($this->loginPath);
    }
}
