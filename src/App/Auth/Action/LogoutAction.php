<?php

declare(strict_types=1);

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Response\RedirectResponse;
use Sorani\SimpleFramework\Session\FlashService;

class LogoutAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;


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
     * @param  RendererInterface $renderer
     * @param DatabaseAuth $databaseAuth
     */
    public function __construct(RendererInterface $renderer, DatabaseAuth $databaseAuth, FlashService $flashService)
    {
        $this->renderer = $renderer;
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
