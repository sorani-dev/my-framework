<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

class BlogAction
{
    use RouterAwareActionTrait;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var Router
     */
    private $router;

    /**
     * BlogAction Contructor
     *
     * @param  RendererInterface $renderer
     * @param  \PDO $pdo
     * @param  Router $router
     */
    public function __construct(RendererInterface $renderer, PostTable $postTable, Router $router)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }

    /**
     * Manage methods to call based on Request attributes
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id');
        if (null === $id) {
            return $this->index($request);
        }
        return $this->show($request);
    }


    /**
     * Show a list of posts
     *
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginated(12, (int)($params['p'] ?? 1));

        return $this->renderer->render('@blog/index', compact('posts'));
    }


    /**
     * Show a post
     * Redirects to good URL for post if slug is incorrect
     *
     * @param  ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function show(ServerRequestInterface $request)
    {
        $slug = $request->getAttribute('slug');
        $post = $this->postTable->find((int)$request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', ['post' => $post]);
    }
}
