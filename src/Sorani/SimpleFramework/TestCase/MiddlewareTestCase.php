<?php

declare(strict_types=1);

namespace Sorani\SimpleFramework\TestCase;

use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * creates a RequestHandlerInterface object in the setUp method as a property
 */
class MiddlewareTestCase extends TestCase
{
    /**
     * @var RequestHandlerInterface
     */
    protected $handler;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();

        $this->handler = $handler;
    }
}
