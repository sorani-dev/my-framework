<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Renderer;

use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Renderer\PHPRenderer as Renderer;

class PHPRendererTest extends TestCase
{
    /**
     * @var Renderer
     */
    private $renderer;

    public function setUp()
    {
        $this->renderer = new Renderer();
    }

    public function testRenderTheRightPath()
    {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Hello everybody! How are you?', $content);
    }

    public function testRenderTheDefaultPath()
    {
        $this->renderer->addPath(__DIR__ . '/views');
        $content = $this->renderer->render('demo');
        $this->assertEquals('Hello everybody! How are you?', $content);
    }

    public function testRenderWithParams()
    {
        $this->renderer->addPath(__DIR__ . '/views');
        $content = $this->renderer->render('demoparams', ['name' => 'Sarah']);
        $this->assertEquals('Hello Sarah', $content);
    }

    public function testGlobalParameters()
    {
        $this->renderer->addPath(__DIR__ . '/views');
        $this->renderer->addGlobal('name', 'Sarah');
        $content = $this->renderer->render('demoparams')
        ;
        $this->assertEquals('Hello Sarah', $content);
    }
}
