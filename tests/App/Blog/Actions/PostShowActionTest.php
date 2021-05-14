<?php

// declare(strict_types=1);

namespace Tests\App\Blog\Actions;

use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
// use Prophecy\PhpUnit\ProphecyTrait;
use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
use Sorani\SimpleFramework\Renderer\RendererInterface;
use Sorani\SimpleFramework\Router;

class PostShowActionTest extends TestCase
{
    // use ProphecyTrait;

    /**
     * @var PostShowAction
     */
    private $action;
    /**
     * @var Renderer
     */
    private $renderer;

    private $postTable;

    private $router;

    public function setUp()
    {
        // RendererInterface
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->renderer->render(Argument::any())->willReturn('');

        $this->postTable = $this->prophesize(PostTable::class);

        // Router
        $this->router = $this->prophesize(Router::class);
        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->postTable->reveal(),
            $this->router->reveal()
        );
    }

    public function makePost($id, $slug)
    {
        //  Post test
        $post = new Post();
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
        $this->postTable->findWithCategory($post->id)->willReturn($post);

        $response = call_user_func($this->action, $request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertContains('/demo2', $response->getHeader('Location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, 'azezezaeza-ezeae');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);

        $this->postTable->findWithCategory($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

        $response = call_user_func($this->action, $request);
        $this->assertEquals('', $response);
    }
}
