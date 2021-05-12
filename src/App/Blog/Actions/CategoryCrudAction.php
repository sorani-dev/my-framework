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

    /**
     * {@inheritDoc}
     */
    protected $viewPath = '@blog/admin/categories';

    /**
     * {@inheritDoc}
     */
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
     * @param Category $item
     */
    protected function getParams(ServerRequestInterface $request, EntityInterface $item): array
    {
        $params = array_filter(
            $request->getParsedBody(),
            fn ($key) => in_array($key, ['name', 'slug']),
            ARRAY_FILTER_USE_KEY
        );
        return $params;
    }

    /**
     * {@inheritDoc}
     */
    protected function getValidator(ServerRequestInterface $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            ->uniqueRecord(
                'slug',
                $this->table->getTable(),
                $this->table->getPdo(),
                (int)$request->getAttribute('id')
            )
            ->slug('slug');
    }

    /**
     * {@inheritDoc}
     */
    protected function getNewEntity(): EntityInterface
    {
        return new Category();
    }
}
