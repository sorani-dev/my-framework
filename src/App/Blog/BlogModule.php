<?php

namespace App\Blog;

use GuzzleHttp\Psr7\Response;
use Sorani\SimpleFramework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogModule
{
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug}', [$this, 'show'], 'blog.show');
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, [], '<h1>Welcome to my Blog!</h1>');
    }

    public function show(ServerRequestInterface $request): string
    {
        $slug = $request->getAttribute('slug');
        return '<h1>Welcome to the post ' . $slug . '</h1>';
    }
}
