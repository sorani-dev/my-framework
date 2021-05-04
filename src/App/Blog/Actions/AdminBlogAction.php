<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

class AdminBlogAction
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
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        $id = $request->getAttribute('id');
        if (null === $id) {
            return $this->index($request);
        }
        return $this->edit($request);
    }


    /**
     * Show a list of posts
     *
     * @return string
     */
    public function index(ServerRequestInterface $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, (int)($params['p'] ?? 1));

        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * Edit a Post
     *
     * @param  ResponseInterface|string $request
     * @return void
     */
    public function edit(ServerRequestInterface $request)
    {
        $item = $this->postTable->find((int)$request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $this->postTable->update($item->id, $params);
            return $this->redirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }

    /**
     * Create a Post
     *
     * @param ServerRequestInterface
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        $item = $this->postTable->find((int)$request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $now = date('Y-m-d H:i:s');
            $params = array_merge($params, [
                'updated_at' => $now,
                'created_at' => $now,
            ]);
            $this->postTable->insert($params);
            return $this->redirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/create', compact('item'));
    }

    public function delete(ServerRequestInterface $request)
    {
        $this->postTable->delete((int)$request->getAttribute('id'));
        return $this->redirect('blog.admin.index');
    }

    private function getParams(ServerRequestInterface $request): array
    {
        return array_filter(
            $request->getParsedBody(),
            fn ($key) => in_array($key, ['name', 'slug', 'content']),
            ARRAY_FILTER_USE_KEY
        );
    }
}
