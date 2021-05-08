<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Router;

use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Router;

class RouterFactory
{
    public function __invoke(ContainerInterface $c)
    {
        $cache = null;
        if ($c->get('env') === 'production') {
            $cache =  'tmp/routes';
        }
        return new Router($cache);
    }
}
