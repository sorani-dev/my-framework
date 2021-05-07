<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Twig\Extension;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Twig\Extension\TimeExtension;
use Twig\Environment;

class TimeExtensionTest extends TestCase
{
    /**
     * @var TimeExtension
     */
    private $timeExtension;

    /**
     * @var Environment
     */
    private $env;


    public function setUp(): void
    {
        $this->env = $this->getMockBuilder(Environment::class)->disableOriginalConstructor()->getMock();
        $this->env
            ->expects($this->any())
            ->method('getCharset')
            ->willReturn('utf-8');
        $this->timeExtension = new TimeExtension($this->env);
    }

    public function testDateFormat()
    {
        $date = new DateTimeImmutable();
        $format = 'd/m/Y H:i';

        $result = sprintf(
            '<time class="need_to_be_rendered" datetime="%s">%s</time>',
            $date->format(DateTimeInterface::ISO8601),
            $date->format($format)
        );
        $this->assertEquals($result, $this->timeExtension->ago($date));
    }
}
