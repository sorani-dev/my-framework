<?php

declare(strict_types=1);

namespace App\Contact\Action;

use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class ContactShow
{
    /**
     * @var RendererInterface
     */
    private $renderer;


    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Show the contact form
     * Redirects to good URL for post if slug is incorrect
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function __invoke(ServerRequestInterface $request)
    {
    }
}
