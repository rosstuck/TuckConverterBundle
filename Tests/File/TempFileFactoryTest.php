<?php

namespace Tuck\ConverterBundle\Tests\File;

use Tuck\ConverterBundle\File\TempFileFactory;

class TempFileUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TempFileFactory
     */
    protected $tempFileFactory;

    /**
     * @var \SplFileObject
     */
    protected $tempFile;

    protected function setUp()
    {
        $this->tempFileFactory = new TempFileFactory();
    }

    public function testCreatingATempFileWorks()
    {
        $this->tempFile = $this->tempFileFactory->createFile();

        $this->assertFileExists($this->tempFile->getRealPath());
    }

    public function testWritingToATempFile()
    {
        $this->tempFile = $this->tempFileFactory->createFile('lorem ipsum');

        $this->assertEquals('lorem ipsum', file_get_contents($this->tempFile->getRealPath()));
    }

    public function testAddingAnExtensionWorks()
    {
        $this->tempFile = $this->tempFileFactory->createFile(null, 'foo');

        $this->assertEquals('foo', $this->tempFile->getExtension());
    }

    protected function tearDown()
    {
        if ($this->tempFile) {
            unlink($this->tempFile->getRealPath());
        }
    }
}
 