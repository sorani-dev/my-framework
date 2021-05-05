<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Validator\Validator;

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
     * @var FlashService
     */
    private $flash;

    /**
     * BlogAction Contructor
     *
     * @param  RendererInterface $renderer
     * @param  \PDO $pdo
     * @param  Router $router
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, PostTable $postTable, Router $router, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
        $this->flash = $flash;
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
        $flash = $this->flash;

        return $this->renderer->render('@blog/admin/index', compact('items', 'flash'));
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
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('The post was successfully updated');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();

            // $item->name = $params['name'];
            // $item->slug = $params['slug'];
            // $item->content = $params['content'];

            $params['id'] = $item->id;
            $item = $params;
        }

        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
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
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->insert($params);
                $this->flash->success('The post was successfully created');
                return $this->redirect('blog.admin.index');
            }

            $errors = $validator->getErrors();

            $item = $params;
        } else {
            // $item = new Post();
            // $item->created_at = date('Y-m-d H:i:s');
        }

        return $this->renderer->render('@blog/admin/create', compact('item', 'errors'));
    }

    /**
     * Delete a Post
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->postTable->delete((int)$request->getAttribute('id'));
        $this->flash->success('The post was successfully deleted');
        return $this->redirect('blog.admin.index');
    }

    /**
     * Filter the Input Parsed body
     * @param ServerRequestInterface $request
     */
    private function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter(
            $request->getParsedBody(),
            fn ($key) => in_array($key, ['name', 'slug', 'content', 'created_at']),
            ARRAY_FILTER_USE_KEY
        );

        $now = date('Y-m-d H:i:s');
        $params = array_merge($params, [
            'updated_at' => $now,
            // 'created_at' => $nPow,
        ]);
        return $params;
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->required('name', 'slug', 'content')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug');
    }
}
