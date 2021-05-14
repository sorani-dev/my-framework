<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Twig\Extension\TextExtension;
use Twig\Environment;

class TestTextExtension extends TestCase
{
    /**
     * @var TextExtension
     */
    private $textExtension;

    /**
     * @var Environment
     */
    private $env;


    public function setUp()
    {
        $this->env = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $this->env
            ->expects($this->any())
            ->method('getCharset')
            ->willReturn('utf-8');
        $this->textExtension = new TextExtension($this->env);
    }

    public function testExcertptWithShortText()
    {
        $text = "Hello";
        $this->assertEquals($text, $this->textExtension->excerpt($text));
    }


    public function testExcertptWithLongText()
    {
        $text = "Hello there everyone";
        $this->assertEquals("Hello there...", $this->textExtension->excerpt($text, 12));
    }


    /**
     * @dataProvider getTruncateTestData
     */
    public function testTruncate($input, $length, $preserve, $separator, $expectedOutput)
    {
        $output = $this->textExtension
            ->truncateFilter($this->env, $input, $length, $preserve, $separator);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getTruncateTestData()
    {
        return [
            ['This is a very long sentence.', 2, false, '...', 'Th...'],
            ['This is a very long sentence.', 6, false, '...', 'This i...'],
            ['This is a very long sentence.', 2, true, '...', 'This...'],
            ['This is a very long sentence.', 2, true, '[...]', 'This[...]'],
            ['This is a very long sentence.', 23, false, '...', 'This is a very long sen...'],
            ['This is a very long sentence.', 23, true, '...', 'This is a very long sentence.'],
        ];
    }
}
