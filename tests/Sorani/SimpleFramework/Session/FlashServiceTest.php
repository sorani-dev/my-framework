<?php

// declare(strict_types=1);

namespace Test\Sorani\SimpleFramework\Session;

use PHPUnit\Framework\TestCase;
use Sorani\SimpleFramework\Session\ArraySession;
use Sorani\SimpleFramework\Session\FlashService;

class FlashServiceTest extends TestCase
{
    /**
     * @var ArraySession
     */
    private $session;

    /**
     * @var FlashService
     */
    private $flash;


    protected function setUp()
    {
        $this->session = new ArraySession();
        $this->flash = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->flash->success('Fantastic!');
        $this->assertEquals('Fantastic!', $this->flash->get('success'));
        $this->assertNull($this->session->get('__FLASH__'));
        $this->assertEquals('Fantastic!', $this->flash->get('success'));
        $this->assertEquals('Fantastic!', $this->flash->get('success'));
    }

    public function testSuccessIsFilled()
    {
        $this->flash->success('Fantastic!');
        $this->assertEquals('Fantastic!', $this->flash->get('success'));
    }

    public function testErrorIsFilled()
    {
        $this->flash->error('I am talking!');
        $this->assertEquals('I am talking!', $this->flash->get('error'));
    }
}
