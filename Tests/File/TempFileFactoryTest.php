<?php

namespace Tuck\ConverterBundle\Tests\File;

use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\File\TempFileFactory;

class TempFileFactoryTest extends \PHPUnit_Framework_TestCase
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
        $this->tempFileFactory = new SysTempFileFactory();
    }

    public function testCreatingTempFileWorks()
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

    /**
     * @expectedException \Tuck\ConverterBundle\Exception\FileAlreadyExistsException
     */
    public function testThrowsExceptionWhenTempFileAlreadyExists()
    {
        // This factory always creates the same file name...
        $factory = new MockTempFileFactory();

        // ...so running it twice here should thrown an exception
        $this->tempFile = $factory->createFile();
        $factory->createFile();
    }

    protected function tearDown()
    {
        if ($this->tempFile) {
            unlink($this->tempFile->getRealPath());
        }
    }
}
