<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use GuzzleHttp\Psr7\Response;
use Sorani\SimpleFramework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class BlogModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config/config.php';

    public const MIGRATIONS = __DIR__ . '/db/migrations';

    public const SEEDS = __DIR__ . '/db/seeds';

    public function __construct(string $prefix, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('blog', __DIR__ . '/resources/views');

        $router->get($prefix, BlogAction::class, 'blog.index');
        $router->get($prefix . '/{slug:[a-z0-9\-]+}', BlogAction::class, 'blog.show');
    }
}
