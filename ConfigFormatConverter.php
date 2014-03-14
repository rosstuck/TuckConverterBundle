<?php
namespace Tuck\ConverterBundle;

use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Tuck\ConverterBundle\Dumper\DumperFactory;
use Tuck\ConverterBundle\Exception\UnknownFileException;
use Tuck\ConverterBundle\File\TempFileFactory;
use Tuck\ConverterBundle\Loader\LoaderFactory;

/**
 * Converts a services config file to another format.
 *
 * Future changes:
 * This class exposes the file part rather heavily because that's how it is
 * in practice but also because that's how the internal loader and dumpers
 * work.
 *
 * This would probably be improved by refactoring it to return an actual
 * ConfigFile object, then we could break the coupling with file extensions
 * in the UI.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class ConfigFormatConverter
{
    /**
     * @var LoaderFactory
     */
    protected $loaderFactory;

    /**
     * @var DumperFactory
     */
    protected $dumperFactory;

    /**
     * @var TempFileFactory
     */
    protected $tempFileFactory;

    public function __construct(
        LoaderFactory $loaderFactory,
        DumperFactory $dumperFactory,
        TempFileFactory $tempFileFactory
    ) {
        $this->loaderFactory = $loaderFactory;
        $this->dumperFactory = $dumperFactory;
        $this->tempFileFactory = $tempFileFactory;
    }

    /**
     * Convert a service config file from one format to another (xml, yml, etc)
     *
     * @param  SplFileInfo                    $file      The file to convert
     * @param  string                         $newFormat Format to convert to, given as a file extension
     * @return string                         Converted config, returned as a raw string
     * @throws Exception\UnknownFileException
     */
    public function convertFile(SplFileInfo $file, $newFormat)
    {
        if (!$file->isFile()) {
            throw UnknownFileException::create($file->getRealPath());
        }
        $container = new ContainerBuilder(new ParameterBag());
        $loader = $this->loaderFactory->createFileLoader($file->getExtension(), $container, $file->getPath());
        $loader->load($file->getFilename());

        return $this->dumperFactory->createDumper($newFormat, $container)->dump();
    }

    /**
     * Convert a config represented as a string to some other format
     *
     * @param  string $content
     * @param  string $oldFormat
     * @param  string $newFormat
     * @return string
     */
    public function convertString($content, $oldFormat, $newFormat)
    {
        $tempFile = $this->tempFileFactory->createFile($content, $oldFormat);

        $output = $this->convertFile($tempFile, $newFormat);
        unlink($tempFile->getRealPath());

        return $output;
    }
}
