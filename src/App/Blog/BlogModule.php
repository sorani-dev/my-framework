<?php

// declare(strict_types=1);

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use Psr\Container\ContainerInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Modules\Module;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class BlogModule extends Module
{
    /**
     * {@inheritdoc}
     */
    const DEFINITIONS = __DIR__ . '/config/config.php';

    /**
     * {@inheritdoc}
     */
    const MIGRATIONS = __DIR__ . '/db/migrations';

    /**
     * {@inheritdoc}
     */
    const SEEDS = __DIR__ . '/db/seeds';

    /**
     * Construct
     *
     * @param  ContainerInterface $c
     * @return void
     */
    public function __construct(ContainerInterface $c)
    {
        $blogPrefix = $c->get('blog.prefix');

        $c->get(RendererInterface::class)->addPath('blog', __DIR__ . '/resources/views');
        $router = $c->get(Router::class);
        $router->get($blogPrefix, PostIndexAction::class, 'blog.index');
        // alto router
        $router->get($blogPrefix . '/[slug:slug]-[i:id]', PostShowAction::class, 'blog.show');
        // mezzio router
        // $router->get($blogPrefix . '/{slug:[a-z0-9\-]+}-{id:\d+}', PostShowAction::class, 'blog.show');
        // aura router
        // $router->get($blogPrefix . '/{slug}-{id}', PostShowAction::class, 'blog.show', ['params' => ['slug' => '[a-z0-9\-]+', 'id' => '\d+']]);
        
        // alto router
        $router->get($blogPrefix . '/category/[slug:slug]', CategoryShowAction::class, 'blog.category');
        // mezzio router
        //  $router->get($blogPrefix . '/category/{slug:[a-z0-9\-]+}', CategoryShowAction::class, 'blog.category'); 
        // aura router
        // $router->get($blogPrefix . '/category/{slug}', CategoryShowAction::class, 'blog.category', ['params' => ['slug' => '[a-z0-9\-]+']]);

        if ($c->has('admin.prefix')) {
            $prefix = $c->get('admin.prefix');
            $router->crud($prefix . '/posts', PostCrudAction::class, 'blog.admin');
            $router->crud($prefix . '/categories', CategoryCrudAction::class, 'blog.admin.category');
        }
    }
}
