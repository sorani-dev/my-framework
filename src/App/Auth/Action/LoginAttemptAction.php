<?php

declare(strict_types=1);

namespace App\Auth\Action;

use App\Auth\DatabaseAuth;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Response\RedirectResponse;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Session\SessionInterface;

class LoginAttemptAction
{
    use RouterAwareActionTrait;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var DatabaseAuth
     */
    private $auth;

    /**
     * @var Router
     */
    private $router;


    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * LoginAttemptAction Contructor
     *
     * @param  RendererInterface $renderer
     * @param  DatabaseAuth $auth
     * @param  Router $router
     * @param  SessionInterface $session
     */
    public function __construct(
        RendererInterface $renderer,
        DatabaseAuth $auth,
        Router $router,
        SessionInterface $session
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * __invoke
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getParsedBody();
        $user = $this->auth->login($params['username'], $params['password']);

        if ($user) {
            (new FlashService($this->session))->success('');
            $redirectionPath = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($redirectionPath);
        } else {
            (new FlashService($this->session))->error('Invalid username or password');
            // (new FlashService($this->session))->error('Identifiant ou mot de passe incorrects');
            return $this->redirect('auth.login');
        }
    }
}
