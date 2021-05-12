<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\Router;

use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Router;

/**
 * Create a Router instance for the DIC
 */
class RouterFactory
{
    /**
     * @param ContainerInterface $c
     * @return Router
     */
    public function __invoke(ContainerInterface $c): Router
    {
        $cache = null;
        if ($c->get('env') === 'production') {
            $cache =  'tmp/routes';
        }
        return new Router($cache);
    }
}
