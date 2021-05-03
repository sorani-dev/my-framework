<?php

declare(strict_types=1);

namespace Tests\App\Blog\Actions;

use App\Blog\Actions\BlogAction;
use App\Blog\Table\PostTable;
use GuzzleHttp\Psr7\ServerRequest;
use PDO;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;
use stdClass;

class BlogActionTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var BlogAction
     */
    private $action;
    /**
     * @var BlogAction
     */
    private $renderer;

    private $postTable;

    private $router;

    public function setUp(): void
    {
        // RendererInterface
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any())->willReturn('');



        $this->postTable = $this->prophesize(PostTable::class);

        // Router
        $this->router = $this->prophesize(Router::class);
        $this->action = new BlogAction(
            $this->renderer->reveal(),
            $this->postTable->reveal(),
            $this->router->reveal()
        );
    }

    public function makePost(int $id, string $slug): stdClass
    {
        //  Post test
        $post = new \stdClass();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }


    public function testShowRedirect()
    {
        $post = $this->makePost(9, 'azezezaeza-ezeae');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');

        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');
        $this->postTable->find($post->id)->willReturn($post);

        $response = call_user_func($this->action, $request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertContains('/demo2', $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, 'azezezaeza-ezeae');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);

        $this->postTable->find($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

        $response = call_user_func($this->action, $request);
        $this->assertEquals('', $response);
    }
}
