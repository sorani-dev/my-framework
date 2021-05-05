<?php

declare(strict_types=1);

use function DI\get;
use function DI\create;
use function DI\factory;
use Sorani\SimpleFramework\Router;

use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Renderer\TwigRendererFactory;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Session\PHPSession;
use Sorani\SimpleFramework\Session\SessionInterface;
use Sorani\SimpleFramework\Twig\Extensions\FlashExtension;
use Sorani\SimpleFramework\Twig\Extensions\FormExtension;
use Sorani\SimpleFramework\Twig\Extensions\PagerFantaExtension;
use Sorani\SimpleFramework\Twig\Extensions\RouterTwigExtension;
use Sorani\SimpleFramework\Twig\Extensions\TextExtension;
use Sorani\SimpleFramework\Twig\Extensions\TimeExtension;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'root',
    'database.name' => 'pratiquepoo',
    'views.path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
        get(\Pagerfanta\Twig\Extension\PagerfantaExtension::class),
        get(PagerFantaExtension::class),
        get(TextExtension::class),
        get(TimeExtension::class),
        get(FlashExtension::class),
        get(FormExtension::class),
    ],
    SessionInterface::class => create(PHPSession::class),
    FlashService::class => create(FlashService::class)->constructor(get(SessionInterface::class)),
    Router::class => create(),
    RendererInterface::class => factory(TwigRendererFactory::class),
    PDO::class => function (ContainerInterface $c) {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s; charset=utf8mb4', $c->get('database.host'), $c->get('database.name')),
            $c->get('database.username'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]
        );
        if ($pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $pdo->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'UTF8'");
        }
        return $pdo;
    },
];
