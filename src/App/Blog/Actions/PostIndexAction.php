<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

/**
 * Show a list of Posts
 */
class PostIndexAction
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
     * @var CategoryTable
     */
    private $categoryTable;

    /**
     * PostIndexAction Contructor
     *
     * @param  RendererInterface $renderer
     * @param  Router $router
     * @param CategoryTable $categoryTable
     */
    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        CategoryTable $categoryTable
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    /**
     * Manage methods to call based on Request attributes
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): string
    {

        $params = $request->getQueryParams();

        $page = $params['p'] ?? 1;

        $posts = $this->postTable->findPublic()->paginate(12, (int)$page);
        $categories = $this->categoryTable->findAll();
        // var_dump($posts->getCurrentPageResults()[0]);die;

        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'page'));
    }
}
