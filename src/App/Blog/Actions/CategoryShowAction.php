<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;

class CategoryShowAction
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
    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        $page = $params['p'] ?? 1;

        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));

        $posts = $this->postTable->findPublicForCategory((int)$category->id)->paginate(12, (int)$page);

        $categories = $this->categoryTable->findAll();

        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}
