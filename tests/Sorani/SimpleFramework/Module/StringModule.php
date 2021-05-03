<?php

namespace Tests\Sorani\SimpleFramework\Module;

use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Router\Route;

class StringModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return 'DEMO';
        }, 'demo');
    }
}
