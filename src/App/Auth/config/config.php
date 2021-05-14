<?php

// declare(strict_types=1);

use App\Auth\DatabaseAuth;
use App\Auth\User;
use App\Auth\ForbiddenMiddleware;
use App\Auth\Twig\Extension\AuthTwigExtension;
use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Auth\AuthInterface;
use Sorani\SimpleFramework\Auth\UserInterface;

use function DI\add;
use function DI\get;
use function DI\object;

return [
    'auth.login' => '/login',
    AuthInterface::class => get(DatabaseAuth::class),
    // UserInterface::class => factory(function(AuthInterface $auth) {
    //     return $auth->getUser();
    // })->parameter('auth', get(AuthInterface::class)),
    UserInterface::class => get(User::class),
    ForbiddenMiddleware::class => object()->constructorParameter('loginPath', get('auth.login')),
    'twig.extensions' => add(
        [get(AuthTwigExtension::class),]
    ),
];
