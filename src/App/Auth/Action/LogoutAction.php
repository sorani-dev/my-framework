<?php

declare(strict_types=1);

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Response\RedirectResponse;
use Sorani\SimpleFramework\Session\FlashService;

/**
 * Logout a User Action
 */
class LogoutAction
{
    /**
     * @var DatabaseAuth
     */
    private $databaseAuth;


    /**
     * @var FlashService
     */
    private $flashService;

    /**
     * LogoutAction Contructor
     *
     * @param DatabaseAuth $databaseAuth
     * @param FlashService $flashService
     */
    public function __construct(DatabaseAuth $databaseAuth, FlashService $flashService)
    {
        $this->databaseAuth = $databaseAuth;
        $this->flashService = $flashService;
    }

    /**
     * __invoke
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $this->databaseAuth->logout();
        // $this->flashService->success('Vous êtes maintenant déconnecté');
        $this->flashService->success('You have been logged out successfully');
        return new RedirectResponse('/');
    }
}
