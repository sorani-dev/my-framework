<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Entity\Category;
use App\Blog\Table\CategoryTable;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\CrudAction;
use Sorani\SimpleFramework\Database\EntityInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Validator\Validator;

class CategoryCrudAction extends CrudAction
{
    /**
     * @var CategoryTable
     */
    protected $table;

    protected $viewPath = '@blog/admin/categories';

    protected $routePrefix = 'blog.admin.category';

    /**
     * CategoryCrudAction Contructor
     *
     * @param  RendererInterface $renderer
     * @param CategoryTable $table Table instance
     * @param  Router $router
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, CategoryTable $table, Router $router, FlashService $flash)
    {
        parent::__construct($renderer, $table, $router, $flash);
    }
    /**
     * Filter the Input Parsed body
     * @param ServerRequestInterface $request
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter(
            $request->getParsedBody(),
            fn ($key) => in_array($key, ['name', 'slug']),
            ARRAY_FILTER_USE_KEY
        );
        return $params;
    }

    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->slug('slug');
    }

    protected function getNewEntity(): EntityInterface
    {
        return new Category();
    }
}
