<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Router;

class RouteTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    public function setUp()
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blog', function () {
            return 'Hello!';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Hello!', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testPostMethod()
    {
        $request = new ServerRequest('POST', '/blog');
        $this->router->post('/blog', function () {
            return 'Hello!';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('Hello!', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfUrlDoesNotExist()
    {
        $request = new ServerRequest('GET', '/blog');
        $this->router->get('/blogazeaze', function () {
            return  'Hello!';
        }, 'blog');
        $route = $this->router->match($request);
        $this->assertNull($route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new ServerRequest('GET', '/blog/my-slug-8');
        $this->router->get('/blog', function () {
            return  'azeaze!';
        }, 'posts');
        $this->router->get('/blog/[slug:slug]-[i:id]', function () {
            return 'Hello!';
        }, 'post.show');
        // $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', fn () => 'Hello!', 'post.show');
        $route = $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('Hello!', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'my-slug', 'id' => '8'], $route->getParams());

        // Invalid URL
        $route = $this->router->match(new ServerRequest('GET', '/blog/my_slug-18'));
        $this->assertNull($route);
    }

    public function testGenerateUri()
    {
        $this->router->get('/blog', function () {
            return 'azeaze!';
        }, 'posts');
        $this->router->get('/blog/[slug:slug]-[i:id]', function () {
            return 'Hello!';
        }, 'post.show');
        // $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', fn () => 'Hello!', 'post.show');
        $uri = $this->router->generateUri('post.show', ['slug' => 'my-post', 'id' => 18]);
        $this->assertEquals('/blog/my-post-18', $uri);
    }

    public function testGenerateUriWithQueryParams()
    {
        $this->router->get('/blog', function () {
            return 'azeaze!';
        }, 'posts');
        $this->router->get('/blog/[slug:slug]-[i:id]', function () {
            return 'Hello!';
        }, 'post.show');
        // $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', fn () => 'Hello!', 'post.show');
        $uri = $this->router->generateUri('post.show', ['slug' => 'my-post', 'id' => 18], ['p' => 2]);
        $this->assertEquals('/blog/my-post-18?p=2', $uri);
    }
}
