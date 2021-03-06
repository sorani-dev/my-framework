<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Action\LoginAction;
use App\Auth\Action\LoginAttemptAction;
use App\Auth\Action\LogoutAction;
use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

class AuthModule extends Module
{
    /**
     * {@inheritdoc}
     */
    public const DEFINITIONS = __DIR__ . '/config/config.php';

    /**
     * {@inheritdoc}
     */
    public const MIGRATIONS = __DIR__ . '/db/migrations';

    /**
     * {@inheritdoc}
     */
    public const SEEDS = __DIR__ . '/db/seeds';

    /**
     * AuthModule Constructor
     *
     * @param  ContainerInterface $container
     * @param  Router $router
     * @param  RendererInterface $renderer
     */
    public function __construct(ContainerInterface $container, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('auth', __DIR__ . '/resources/views');
        $router->get($container->get('auth.login'), LoginAction::class, 'auth.login');
        $router->post($container->get('auth.login'), LoginAttemptAction::class);
        $router->post('/logout', LogoutAction::class, 'auth.logout');
    }
}
