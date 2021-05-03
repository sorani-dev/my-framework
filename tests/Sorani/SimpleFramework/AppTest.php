<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework;

use App\Blog\BlogModule;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Sorani\SimpleFramework\App;
use Tests\Sorani\SimpleFramework\Module\ErrorModule;
use Tests\Sorani\SimpleFramework\Module\StringModule;

class AppTest extends TestCase
{
    public function testRedirectTrailingSlash()
    {
        $app = new App();
        $request = new ServerRequest('GET', '/azeaze/');
        $response = $app->run($request);
        $this->assertContains('/azeaze', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog()
    {
        $app = new App([
            BlogModule::class
        ]);
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);

        $this->assertEquals('<h1>Welcome to my Blog!</h1>', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());

        $requestSingle = new ServerRequest('GET', '/blog/the-test-post');
        $responseSingle = $app->run($requestSingle);
        $this->assertEquals('<h1>Welcome to the post the-test-post</h1>', (string)$responseSingle->getBody());
    }

    public function testThrowExceptionIfNoValidResponseSent()
    {
        $app = new App([StringModule::class]);

        $response = $app->run(new ServerRequest('GET', '/demo'));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('DEMO', (string)$response->getBody());
    }

    public function testConvertStringToResponse()
    {
        $app = new App([ErrorModule::class]);
        $this->expectException(\Exception::class);
        $response = $app->run(new ServerRequest('GET', '/demo'));
    }

    public function testNotFound()
    {
        $app = new App();
        $request = new ServerRequest('GET', '/aze');
        $response = $app->run($request);
        $this->assertEquals('<h1>Erreur 404</h1>', (string)$response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }
}
