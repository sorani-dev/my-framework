<?php

// // declare(strict_types=1);

namespace App\Admin;

use App\Admin\Actions\DashboardAction;
use App\Admin\Twig\Extension\AdminTwigExtension;
use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Renderer\TwigRenderer;
use Sorani\SimpleFramework\Router;

class AdminModule extends Module
{
    /**
     * {@inheritDoc}
     */
    const DEFINITIONS = __DIR__ . '/config/config.php';

    /**
     * AdminModule Constructor
     *
     * @param RendererInterface $renderer
     * @param Router $router
     * @param AdminTwigExtension $adminTwigExtension
     * @param string $prefix
     */
    public function __construct(
        RendererInterface $renderer,
        Router $router,
        AdminTwigExtension $adminTwigExtension,
        $prefix
    ) {
        $renderer->addPath('admin', __DIR__ . '/resources/views');
        $router->get($prefix, DashboardAction::class, 'admin');

        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
