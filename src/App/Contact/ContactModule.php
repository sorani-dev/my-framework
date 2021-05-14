<?php

// declare(strict_types=1);

namespace App\Contact;

use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

class ContactModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    /**
     * Contructor
     *
     * @param  Router $router
     * @param  RendererInterface $renderer
     */
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('contact', __DIR__ . '/resouces/views');
        $router->get('/contact', ContactShow::class, 'contact.show');
        $router->post('/contact', ContactPost::class, 'contact.show');
    }
}
