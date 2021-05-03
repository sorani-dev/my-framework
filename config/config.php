<?php

use Mezzio\Router\Route;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Renderer\TwigRendererFactory;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Twig\Extensions\RouterTwigExtension;

use function DI\create;
use function DI\factory;
use function DI\get;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'root',
    'database.name' => 'pratiquepoo',
    'views.path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
    ],
    Router::class => create(),
    RendererInterface::class => factory(TwigRendererFactory::class),
];
