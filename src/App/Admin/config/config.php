<?php

use App\Admin\Actions\DashboardAction;
use App\Admin\AdminModule;
use App\Admin\Twig\Extension\AdminTwigExtension;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

use function DI\add;
use function DI\get;
use function DI\object;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => object()->constructor(get('admin.widgets')),
    AdminModule::class => object()->constructor(
        get(RendererInterface::class),
        get(Router::class),
        get(AdminTwigExtension::class),
        get('admin.prefix')
    ),
    DashboardAction::class => object()->constructor(get(RendererInterface::class), get('admin.widgets')),

];
