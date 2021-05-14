<?php

// declare(strict_types=1);

namespace Tests\Sorani\SimpleFramework\Http;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Sorani\SimpleFramework\Http\Upload;

class UploadTest extends TestCase
{
   /**
    * @var Upload
    */
    private $upload;

    public function setUp()
    {
        $this->upload = new Upload('tests');
    }

    public function tearDown()
    {
        if (file_exists('tests/demo.jpg')) {
            unlink('tests/demo.jpg');
        }
    }

    public function testUpload()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
        $uploadedFile->expects($this->any())->method('getClientFileName')
        ->willReturn('demo.jpg');

        $uploadedFile->expects($this->once())->method('moveTo')
        ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo.jpg'));
        $uploadedFile->method('getError')->willReturn(UPLOAD_ERR_OK);
        $this->assertEquals('demo.jpg', $this->upload->upload($uploadedFile));
    }

    public function testUploadWithExistingFile()
    {
        touch('tests/demo.jpg');
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
        $uploadedFile->expects($this->any())->method('getClientFileName')
        ->willReturn('demo.jpg');

        $uploadedFile->expects($this->once())->method('moveTo')
        ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo_copy.jpg'));
        $uploadedFile->method('getError')->willReturn(UPLOAD_ERR_OK);
        $this->assertEquals('demo_copy.jpg', $this->upload->upload($uploadedFile));
    }

    public function testDontMoveIfFileNotUploaded()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();

        $uploadedFile->expects($this->any())->method('getClientFileName')
        ->willReturn('demo.jpg');

        $uploadedFile->expects($this->never())->method('moveTo')
        ->with($this->equalTo('tests' . DIRECTORY_SEPARATOR . 'demo_copy.jpg'));
        $uploadedFile->method('getError')->willReturn(UPLOAD_ERR_CANT_WRITE);
        $this->assertNull($this->upload->upload($uploadedFile));
    }
}
