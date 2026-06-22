<?php

declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Database\Query;

use Sorani\SimpleFramework\Database\Query\Hydrator;
use Sorani\SimpleFramework\TestCase\ExtendedTestCase;
use Tests\Sorani\SimpleFramework\Database\fixtures\Demo;

class HydratorTest extends ExtendedTestCase
{
    protected $table;
    protected Hydrator $hydrator;

    public function setUp(): void
    {
        parent::setUp();
        // $this->pdo = $this->getTestDatabase();
        // $this->seed();
        $this->hydrator = Hydrator::getInstance();
    }

    public function testHydratorCreateInstance(): void
    {
        $this->assertInstanceOf(Hydrator::class, $this->hydrator);
    }

    public function testIsSingleton(): void
    {
        $this->assertInstanceOf(Hydrator::class, Hydrator::getInstance());
        $this->assertSame($this->hydrator, Hydrator::getInstance());
    }

    public function testHydrateArray(): void
    {
        $this->assertInstanceOf(
            \stdClass::class,
            $this->hydrator->hydrate(['id' => 1, 'name' => 'test'], \stdClass::class)
        );
        $this->assertObjectHasProperty('id', $this->hydrator->hydrate(['id' => 1, 'name' => 'test'], \stdClass::class));
    }

    public function testgetProperty(): void
    {
        $this->assertEquals('CategoryId', $this->hydrator->getProperty('category_id'));
        $this->assertEquals('SlugEn', $this->hydrator->getProperty('slug_en'));
    }

    public function testGetSetter(): void
    {
        $this->assertEquals('setCategoryId', $this->hydrator->getSetter('category_id'));
        $this->assertEquals('setSlugEn', $this->hydrator->getSetter('slug_en'));
    }

    public function testConvertValue(): void
    {
        $demo = new Demo();
        $this->assertEquals(1, $this->hydrator->convertValue($demo, 'id', 1));
        $this->assertEquals(1, $this->hydrator->convertValue($demo, 'id', '1'));
        $this->assertEquals('test', $this->hydrator->convertValue($demo, 'slug', 'test'));
    }
}
