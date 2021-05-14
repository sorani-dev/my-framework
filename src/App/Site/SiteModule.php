<?php

// declare(strict_types=1);

namespace App\Site;

use App\Site\Action\IndexAction;
use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

/**
 * Class for the base site
 */
class SiteModule extends Module
{
    /**
     * @const Definitions path for PHP-DI Config
     */
    const DEFINITIONS = null;

    // /**
    //  * Migrations folder for Phinx
    //  */
    // const MIGRATIONS = null;

    // /**
    //  * Seed folder for Phinx
    //  */
    // const SEEDS = null;

    /**
     * Constructor
     *
     * @param  RendererInterface $renderer
     * @param  Router $router
     */
    public function __construct(RendererInterface $renderer, Router $router)
    {
        $renderer->addPath('site', __DIR__ . '/resources/views');
        $router->get('/', IndexAction::class, 'site.index');
    }
}
