<?php

declare(strict_types=1);

use App\Blog\BlogModule;
use App\Blog\Twig\Extensions\DemoExtension;

use function DI\add;
use function DI\get;

return [
    'blog.prefix' => '/blog',
    'twig.extensions' => add([
        get(DemoExtension::class),
    ]),
    BlogModule::class => \DI\autowire()->constructorParameter('prefix', get('blog.prefix'))
];
