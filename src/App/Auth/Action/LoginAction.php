<?php

declare(strict_types=1);

namespace App\Auth\Action;

use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class LoginAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * LoginAction Contructor
     *
     * @param  RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * __invoke
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        return $this->renderer->render('@auth/login');
    }
}
