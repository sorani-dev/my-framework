<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Actions;

use GuzzleHttp\Psr7\ServerRequest;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Sorani\SimpleFramework\Actions\CrudAction;
use Sorani\SimpleFramework\Database\Table;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;
use Sorani\SimpleFramework\Session\FlashService;
use Sorani\SimpleFramework\TestCase\DatabaseTestCase;
use Sorani\SimpleFramework\TestCase\ExtendedTestCase;

class CrudActionTest extends ExtendedTestCase
{
    use ProphecyTrait;

    /**
     * @var CrudAction
     */
    private $action;

    private $renderer;

    private $CrudTable;

    private $router;

    private $flash;

    protected function setUp(): void
    {
        // RendererInterface
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any())->willReturn('');



        $this->CrudTable = $this->prophesize(Table::class);

        // Router
        $this->router = $this->prophesize(Router::class);
        $this->flash = $this->prophesize(FlashService::class);
        $this->action = $this->getMockForAbstractClass(CrudAction::class, [
            $this->renderer->reveal(),
            $this->CrudTable->reveal(),
            $this->router->reveal(),
            $this->flash->reveal()
        ]);
        $this->setProtectedProperty(
            $this->action,
            'viewPath',
            '@crud'
        );
        $this->setProtectedProperty($this->action, 'viewPath', 'crud');
    }

    public function makeEntity(int $id, string $slug): \stdClass
    {
        //  Post test
        $Crud = new \stdClass();
        $Crud->id = $id;
        $Crud->slug = $slug;
        return $Crud;
    }


    public function testIndex()
    {
        $entity = $this->makeEntity(9, 'azezezaeza-ezeae');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $entity->id)
            ->withAttribute('slug', 'demo');

        $this->router->generateUri('crud.show', ['id' => $entity->id, 'slug' => $entity->slug])->willReturn('/demo2');
        $this->CrudTable->find($entity->id)->willReturn($entity);

        $response = call_user_func($this->action, $request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertContains('/demo2', $response->getHeader('Location'));
    }

    public function testShowRender()
    {
        $entity = $this->makeEntity(9, 'azezezaeza-ezeae');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $entity->id)
            ->withAttribute('slug', $entity->slug);

        $this->CrudTable->find($entity->id)->willReturn($entity);
        $this->renderer->render('@crud/show', ['item' => $entity, 'errors' => []])->willReturn('');

        $response = call_user_func($this->action, $request);
        $this->assertEquals('', $response);
    }
}
