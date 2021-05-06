<?php

declare(strict_types=1);

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\CrudAction;
use Sorani\SimpleFramework\Database\EntityInterface;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Validator\Validator;

class PostCrudAction extends CrudAction
{
    /**
     * @var PostTable
     */
    protected $table;

    protected $viewPath = '@blog/admin/posts';

    protected $routePrefix = 'blog.admin';

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    /**
     * PostCrudAction Contructor
     *
     * @param  RendererInterface $renderer
     * @param PostTable $table Table instance
     * @param  Router $router
     * @param FlashService $flash
     */
    public function __construct(
        RendererInterface $renderer,
        PostTable $table,
        Router $router,
        FlashService $flash,
        CategoryTable $categoryTable
    ) {
        parent::__construct($renderer, $table, $router, $flash);
        $this->categoryTable = $categoryTable;
    }
    /**
     * Filter the Input Parsed body
     * @param ServerRequestInterface $request
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        $params = array_filter(
            $request->getParsedBody(),
            fn ($key) => in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id']),
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
        return parent::getValidator($request)
            ->required('name', 'slug', 'content', 'created_at', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            // ->existsKey('category_id', $this->categoryTable)
            ->existsRecord('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->dateTime('created_at')
            ->slug('slug');
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findAsList();
        $params['categories']['14747447'] = 'Catégorie fake';
        return $params;
    }

    protected function getNewEntity(): EntityInterface
    {
        $post = new Post();
        $post->created_at = new \DateTimeImmutable();
        return $post;
    }
}
