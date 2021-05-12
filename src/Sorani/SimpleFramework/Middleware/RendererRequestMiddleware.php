<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

/**
 * Add a domain to the Server Request
 */
class RendererRequestMiddleware implements MiddlewareInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Construct
     *
     * @param  RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $domain = sprintf(
            '%s://%s%s',
            $request->getUri()->getScheme(),
            $request->getUri()->getHost(),
            $request->getUri()->getPort() ? ':' . $request->getUri()->getPort() : '',
        );
        $this->renderer->addGlobal('domain', $domain);
        return $handler->handle($request);
    }
}
