<?php

// declare(strict_types=1);

namespace App\Admin\Twig\Extension;

use App\Admin\AdminWidgetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminTwigExtension extends AbstractExtension
{
    /**
     * @var AdminWidgetInterface[]
     */
    private $widgets;

    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }

    /**
     * getFunctions
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * renderMenu
     *
     * @return string
     */
    public function renderMenu()
    {
        return array_reduce($this->widgets, function ($html, AdminWidgetInterface $widget) {
            return $html . $widget->renderMenu();
        }, '');
    }
}
