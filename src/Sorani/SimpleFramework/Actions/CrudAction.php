<?php

// declare(strict_types=1);

namespace Sorani\SimpleFramework\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Sorani\SimpleFramework\Actions\RouterAwareActionTrait;
use Sorani\SimpleFramework\Database\EntityInterface;
use Sorani\SimpleFramework\Database\Query\Hydrator;
use Sorani\SimpleFramework\Database\Table;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\Validator\Validator;

/**
 * Creates CRUD Actions for child classes
 * @abstract
 */
abstract class CrudAction
{
    use RouterAwareActionTrait;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Table (eg: PostTable)
     * @var Table Table
     */
    protected $table;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var FlashService
     */
    protected $flash;

    /**
     * Path to the views (eg: @blog/admin)
     * @var string|null
     */
    protected $viewPath;

    /**
     * Prefix to the CRUD routes (eg: blog.admin)
     * @var string
     */
    protected $routePrefix;

    /**
     * Flash messages
     * @var string[]
     */
    protected $messages = [
        // 'create' => 'The item was created successfully',
        'create' => 'The item has been successfully created',
        // 'edit' => 'The item was successfully updated',
        'edit' => 'The item has been edited successfully',
        // 'delete' => 'The post was successfully deleted',
        'delete' => 'The item has been successfully deleted',
    ];

    /**
     * CrudAction Contructor
     *
     * @param RendererInterface $renderer
     * @param Table $table Table instance
     * @param Router $router
     * @param FlashService $flash
     */
    public function __construct(RendererInterface $renderer, Table $table, Router $router, FlashService $flash)
    {
        $this->renderer = $renderer;
        $this->table = $table;
        $this->router = $router;
        $this->flash = $flash;
    }

    /**
     * Manage methods to call based on Request attributes
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function __invoke(ServerRequestInterface $request)
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

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
     * Show a list of items
     *
     * @return string
     */
    public function index(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        $items = $this->table->findAll()->paginate(12, (int)(isset($params['p']) ? $params['p'] : 1));
        $flash = $this->flash;

        return $this->renderer->render($this->viewPath . '/index', compact('items', 'flash'));
    }

    /**
     * Edit an Item
     *
     * @param  ResponseInterface $request
     * @return ResponseInterface|string
     */
    public function edit(ServerRequestInterface $request)
    {
        $item = $this->table->find((int)$request->getAttribute('id'));
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update((int)$item->id, $this->getParams($request, $item));
                $this->flash->success($this->messages['edit']);

                return $this->redirect($this->routePrefix . '.index');
            }
            $errors = $validator->getErrors();

            // $item->name = $params['name'];
            // $item->slug = $params['slug'];
            // $item->content = $params['content'];

            $item = Hydrator::getInstance()->hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Create an Item
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface|string
     */
    public function create(ServerRequestInterface $request)
    {
        $item = $this->getNewEntity();
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->insert($this->getParams($request, $item));
                $this->flash->success($this->messages['create']);
                return $this->redirect($this->routePrefix . '.index');
            }

            $errors = $validator->getErrors();

            Hydrator::getInstance()->hydrate($request->getParsedBody(), $item);
        }

        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Delete a Item
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request)
    {
        $this->table->delete((int)$request->getAttribute('id'));
        $this->flash->success($this->messages['delete']);
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Filter the Input Parsed body
     *
     * @param ServerRequestInterface $request
     * @param EntityInterface $item
     */
    protected function getParams(ServerRequestInterface $request, EntityInterface $item)
    {
        return
            array_filter(
                $request->getParsedBody(),
                function ($key) {
                    return in_array($key, []);
                },
                ARRAY_FILTER_USE_KEY
            );
    }

    /**
     * Generate the Validator to validate the data from the request
     *
     * @param  ServerRequestInterface $request
     * @return Validator
     */
    protected function getValidator(ServerRequestInterface $request)
    {
        return new Validator(array_merge($request->getParsedBody(), $request->getUploadedFiles()));
    }

    /**
     * Handles the parameters sent to the vue
     *
     * @param  array $params
     * @return array
     */
    protected function formParams(array $params)
    {
        return $params;
    }

    /**
     * Create an new Entity for the creation action
     *
     * @return EntityInterface
     */
    abstract protected function getNewEntity();
}
