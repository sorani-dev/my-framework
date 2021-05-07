<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Twig\Extension\FormExtension;

class FormExtensionTest extends TestCase
{
    /**
     * @var FormExtension
     */
    private $formExtension;

    protected function setUp(): void
    {
        $this->formExtension = new FormExtension();
    }

    private function trimString(string $value): string
    {
        $lines = explode(PHP_EOL, $value);
        $lines = array_map('trim', $lines);
        return str_replace(['\n', '\r\n'], [' '], implode('', $lines));
    }

    protected function assertSimilarString(string $expected, string $actual): void
    {
        $this->assertEquals($this->trimString($expected), $this->trimString($actual));
    }

    public function testInput()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Title');
        $expected = '<div class="mb-3">
    <label for="name">Title</label>
    <input type="text" class="form-control" name="name" id="name" value="demo">
</div>';
        $this->assertSimilarString($expected, $html);
    }


    public function testInputWithErrors()
    {
        $context = ['errors' => ['name' => 'the error']];
        $html = $this->formExtension->field($context, 'name', 'demoError', 'Title');
        $expected = '<div class="mb-3 has-danger">
    <label for="name">Title</label>
    <input type="text" class="form-control is-invalid"' .
        ' name="name" id="name" ' .
        'aria-describedby="nameFieldFeedback" value="demoError">' .
        '<small class="invalid-feedback" id="nameFieldFeedback">the error</small>
</div>';
        $this->assertSimilarString($expected, $html);
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'content', 'demo', 'Content', ['type' => 'textarea']);
        $expected = '<div class="mb-3">
    <label for="content">Content</label>
    <textarea class="form-control" name="content" id="content" rows="10">demo</textarea>
</div>';
        $this->assertSimilarString($expected, $html);
    }

    public function testInputWithClass()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            'demo',
            'Title',
            ['class' => 'demo']
        );
        $expected = '<div class="mb-3">
    <label for="name">Title</label>
    <input type="text" class="form-control demo" name="name" id="name" value="demo">
</div>';
        $this->assertSimilarString($expected, $html);
    }

    public function testSelect()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            '1',
            'Title',
            ['options' => [1 => 'Demo', '2' => 'Demo2']]
        );

        $this->assertSimilarString('<div class="mb-3">
    <label for="name">Title</label>
    <select class="form-control" name="name" id="name">' .
        '<option value="1" selected>Demo</option><option value="2">Demo2</option></select>
</div>', $html);
        $html = $this->formExtension->field(
            [],
            'name',
            2,
            'Title',
            ['options' => [1 => 'Demo', '2' => 'Demo2'], "class" => "custom-select"]
        );

        $this->assertSimilarString('<div class="mb-3">
    <label for="name">Title</label>
    <select class="custom-select" name="name" id="name">' .
        '<option value="1">Demo</option><option value="2" selected>Demo2</option></select>
</div>', $html);
    }
}