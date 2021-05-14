<?php

// declare(strict_types=1);

namespace App\Blog;

use App\Admin\AdminWidgetInterface;
use App\Blog\Table\PostTable;
use Sorani\SimpleFramework\Renderer\RendererInterface;

/**
 * {@inheritdoc}
 */
class BlogWidget implements AdminWidgetInterface
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * Constructor
     *
     * @param  RendererInterface $renderer
     * @param  PostTable $postTable
     */
    public function __construct(RendererInterface $renderer, PostTable $postTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function render()
    {
        $count = $this->postTable->count();
        return $this->renderer->render('@blog/admin/widget', compact('count'));
    }

    /**
     * {@inheritDoc}
     */
    public function renderMenu()
    {
        return $this->renderer->render('@blog/admin/menu');
    }
}
