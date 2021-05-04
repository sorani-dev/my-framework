<?php

declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\AdminBlogAction;
use App\Blog\Actions\BlogActionIndex;
use App\Blog\Actions\BlogActionShow;
use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class BlogModule extends Module
{
    public const DEFINITIONS = __DIR__ . '/config/config.php';

    public const MIGRATIONS = __DIR__ . '/db/migrations';

    public const SEEDS = __DIR__ . '/db/seeds';

    public function __construct(ContainerInterface $c)
    {
        $blogPrefix = $c->get('blog.prefix');

        $c->get(RendererInterface::class)->addPath('blog', __DIR__ . '/resources/views');
        $router = $c->get(Router::class);
        $router->get($blogPrefix, BlogActionIndex::class, 'blog.index');
        $router->get($blogPrefix . '/{slug:[a-z0-9\-]+}-{id:\d+}', BlogActionShow::class, 'blog.show');

        if ($c->has('admin.prefix')) {
            $prefix = $c->get('admin.prefix');
            $router->crud($prefix . '/posts', AdminBlogAction::class, 'blog.admin');
        }
    }
}
