<?php
namespace Tuck\ConverterBundle\Tests;

use SplFileInfo;
use Tuck\ConverterBundle\ConfigFormatConverter;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;
use Tuck\ConverterBundle\Tests\File\MockTempFileFactory;

class ConfigFormatConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigFormatConverter
     */
    protected $converter;

    /**
     * A list of files to be removed during teardown
     * @var string[]
     */
    protected $teardownFiles = array();

    public function setup()
    {
        $this->converter = new ConfigFormatConverter(
            new StandardLoaderFactory(),
            new StandardDumperFactory(),
            new SysTempFileFactory()
        );
    }

    public function testConvertingXmlToYml()
    {
        $newConfig = $this->converter->convertFile($this->loadConfigFileMock('simple.xml'), 'yml');

        $this->assertStringEqualsFile(
            $this->loadConfigFileMock('simple.yml')->getRealPath(),
            $newConfig
        );
    }

    /**
     * @expectedException \Tuck\ConverterBundle\Exception\UnknownFileException
     */
    public function testGivingInvalidFileGivesError()
    {
        $this->converter->convertFile(new SplFileInfo('fakefile.xml'), 'yml');
    }

    /**
     * @expectedException \Tuck\ConverterBundle\Exception\UnknownFormatException
     */
    public function testInvalidFormatGivesError()
    {
        $this->converter->convertFile($this->loadConfigFileMock('simple.xml'), 'fakeformat');
    }

    public function testCanConvertString()
    {
        $file = $this->loadConfigFileMock('simple.xml');

        $newConfig = $this->converter->convertString(
            file_get_contents($file->getRealPath()),
            'xml',
            'yml'
        );

        $this->assertStringEqualsFile(
            $this->loadConfigFileMock('simple.yml')->getRealPath(),
            $newConfig
        );
    }

    /**
     * When converting a string, we have to use a temp file since the symfony
     * loaders have that assumption built in. To prevent flooding the tmp dir
     * of the online converter, we should ensure the file is removed afterwards
     */
    public function testTempFileIsCleanedUpAfterConversion()
    {
        // Same setup but with a mock temp file factory that shows us the temp file name used
        $mockTempFileFactory = new MockTempFileFactory();
        $converter = new ConfigFormatConverter(new StandardLoaderFactory(), new StandardDumperFactory(), $mockTempFileFactory);

        // Make sure the file gets removed, even if the test fails so it doesn't jam the next run
        $this->teardownFiles[] = $mockTempFileFactory->getMockFilename('xml');

        // Standard string conversion, nothing to note
        $converter->convertString(
            file_get_contents($this->loadConfigFileMock('simple.xml')->getRealPath()),
            'xml',
            'yml'
        );

        // Ensure the file is gone
        $this->assertFileNotExists($mockTempFileFactory->getMockFilename('xml'));
    }

    public function testTempFileIsCleanedUpEvenWhenConversionFails()
    {
        $mockTempFileFactory = new MockTempFileFactory();
        $converter = new ConfigFormatConverter(new StandardLoaderFactory(), new StandardDumperFactory(), $mockTempFileFactory);
        $this->teardownFiles[] = $mockTempFileFactory->getMockFilename('xml');

        // Standard string conversion, nothing to note
        $gotException = false;
        try {
            $converter->convertString('i am invalid services markup', 'xml', 'yml');
        } catch (\Exception $e) {
            $gotException = true;
        }

        $this->assertTrue($gotException, 'Exception should be fired for invalid markup');
        $this->assertFileNotExists($mockTempFileFactory->getMockFilename('xml'));
    }

    protected function tearDown()
    {
        foreach ($this->teardownFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->teardownFiles = array();
    }

    protected function loadConfigFileMock($filename)
    {
        return new SplFileInfo(__DIR__.'/../Resources/mock_config/'.$filename);
    }
}
