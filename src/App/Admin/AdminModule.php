<?php

declare(strict_types=1);

namespace App\Admin;

use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class AdminModule extends Module
{
    /**
     * @const Definitions path for PHP-DI Config
     */
    public const DEFINITIONS = __DIR__ . '/config/config.php';

    public function __construct(RendererInterface $renderer)
    {
        $renderer->addPath('admin', __DIR__ . '/resources/views');
    }
}
