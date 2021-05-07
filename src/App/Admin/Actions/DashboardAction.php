<?php

declare(strict_types=1);

namespace App\Admin\Actions;

use App\Admin\AdminWidgetInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class DashboardAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;


    /**
     * @var AdminWidgetInterface[]
     */
    private $widgets;

    /**
     * DashboardAction Controller
     *
     * @param  RendererInterface $renderer
     * @param  AdminWidgetInterface[] $widgets
     */
    public function __construct(RendererInterface $renderer, array $widgets)
    {
        $this->renderer = $renderer;
        $this->widgets = $widgets;
    }

    /**
     * __invoke
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): string
    {
        $widgets = array_reduce($this->widgets, function (string $html, AdminWidgetInterface $widget) {
            return $html . $widget->render();
        }, '');
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
