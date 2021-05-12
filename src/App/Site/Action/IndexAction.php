<?php

declare(strict_types=1);

namespace App\Site\Action;

use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class IndexAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Constructor
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
        return $this->renderer->render('@site/index');
    }
}
