<?php

namespace Tests\Sorani\SimpleFramework\Module;

use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Router\Route;

class ErrorModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', fn () => new \stdClass(), 'demo');
    }
}
