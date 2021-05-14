<?php

// declare(strict_types=1);

namespace Test\Sorani\SimpleFramework\Session;

use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Session\ArraySession;

class ArraySessionTest extends TestCase
{
    /**
     * @var PhpSession
     */
    private $session;


    protected function setUp()
    {
        $this->session = new ArraySession();
    }

    public function testDeleteSessionKeyIfNotExists()
    {
        $this->session->delete('me');
        $this->assertNull($this->session->get('me'));
    }

    public function testDeleteSessionKeyIExists()
    {
        $this->session->set('me', 'Allons-y!');
        $this->session->delete('me');
        $this->assertNull($this->session->get('me'));
    }

    public function testSessionKeyIsFilled()
    {
        $this->session->set('me', 'Allons-y!');
        $this->assertEquals('Allons-y!', $this->session->get('me'));
    }

    public function testSessionChangeNotAString()
    {
        $p = new \stdClass();
        $p->name = 'John';
        $this->session->set('me', $p);
        $this->assertInstanceOf(\stdClass::class, $this->session->get('me'));
        $this->assertEquals($p->name, $this->session->get('me')->name);
    }
    public function testSuccessAsArray()
    {
        $p = ['Ami', 'Minako', "Usagi", 'Rei', 'Makoto', 'Hotaru', 'Michiru', 'Haruka', 'Setsuna', 'Earth' => 'Mamoru'];
        $this->session->set('senshi', $p);
        $this->assertEquals($p, $this->session->get('senshi'));
        $this->assertContains('Hotaru', $this->session->get('senshi'));
        $this->assertArrayHasKey('Earth', $this->session->get('senshi'));
        $this->assertEquals('Rei', $this->session->get('senshi')[3]);
        $this->assertEquals('Mamoru', $this->session->get('senshi')['Earth']);
    }
}
