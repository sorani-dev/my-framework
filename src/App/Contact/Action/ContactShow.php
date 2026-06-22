<?php

declare(strict_types=1);

namespace App\Contact\Action;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class ContactShow
{
    /**
     * Constructor
     *
     * @param  RendererInterface $renderer
     * @return void
     */
    public function __construct(private readonly RendererInterface $renderer)
    {
    }

    /**
     * Show the contact form
     * Redirects to good URL for post if slug is incorrect
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new Response();
    }
}
