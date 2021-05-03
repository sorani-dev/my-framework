<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class BlogAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $slug = $request->getAttribute('slug');
        if (null === $slug) {
            return $this->index();
        }
        return $this->show($slug);
    }

    public function index(): string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(string $slug): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $slug
        ]);
    }
}
