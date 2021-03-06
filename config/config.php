<?php

declare(strict_types=1);

use function DI\autowire;
use function DI\get;
use function DI\create;
use function DI\env;
use function DI\factory;

use App\Blog\Table\CategoryTable;
use Sorani\SimpleFramework\Router;

use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Database\Table;
use Sorani\SimpleFramework\Middleware\CsrfMiddleware;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Renderer\TwigRendererFactory;
use Sorani\SimpleFramework\Router\RouterFactory;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Session\PHPSession;
use Sorani\SimpleFramework\Session\SessionInterface;
use Sorani\SimpleFramework\Twig\Extension\CsrfExtension;
use Sorani\SimpleFramework\Twig\Extension\FlashExtension;
use Sorani\SimpleFramework\Twig\Extension\FormExtension;
use Sorani\SimpleFramework\Twig\Extension\PagerFantaExtension;
use Sorani\SimpleFramework\Twig\Extension\RouterTwigExtension;
use Sorani\SimpleFramework\Twig\Extension\TextExtension;
use Sorani\SimpleFramework\Twig\Extension\TimeExtension;

return [
    'env' => env('ENV', 'production'),
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
        get(CsrfExtension::class),
    ],
    SessionInterface::class => create(PHPSession::class),
    FlashService::class => create(FlashService::class)->constructor(get(SessionInterface::class)),
    Router::class => factory(RouterFactory::class),
    CsrfMiddleware::class => autowire()->constructorParameter('session', get(SessionInterface::class)),
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
