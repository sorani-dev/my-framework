<?php

use App\Admin\Actions\DashboardAction;
use App\Admin\AdminModule;
use App\Admin\Twig\Extension\AdminTwigExtension;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

use function DI\add;
use function DI\create;
use function DI\get;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => create()->constructor(get('admin.widgets')),
    AdminModule::class => create()->constructor(
        get(RendererInterface::class),
        get(Router::class),
        get(AdminTwigExtension::class),
        get('admin.prefix')
    ),
    DashboardAction::class => create()->constructor(get(RendererInterface::class), get('admin.widgets')),

];
