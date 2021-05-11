<?php

declare(strict_types=1);

use App\Auth\{
    DatabaseAuth,
    User,
    ForbiddenMiddleware
};
use App\Auth\Twig\Extension\AuthTwigExtension;
use Sorani\SimpleFramework\Auth\{
    AuthInterface,
    UserInterface
};

use function DI\add;
use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    'auth.login' => '/login',
    AuthInterface::class => get(DatabaseAuth::class),
    UserInterface::class => create(User::class),
    ForbiddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth.login')),
    'twig.extensions' => add(
        get(AuthTwigExtension::class),
    ),
];
