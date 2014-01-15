<?php
namespace Tuck\ConverterBundle;

use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Tuck\ConverterBundle\Dumper\DumperFactory;
use Tuck\ConverterBundle\Exception\UnknownFileException;
use Tuck\ConverterBundle\Loader\LoaderFactory;

/**
 * Converts a services config file to another format.
 *
 * Future changes:
 * This class exposes the file part rather heavily because that's how it is
 * in practice but also because that's how the internal loader and dumpers
 * work. Still, this could be used to build a site based converter, perhaps
 * with an addition to the interface or the temp stream.
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

    public function __construct(LoaderFactory $loaderFactory, DumperFactory $dumperFactory)
    {
        $this->loaderFactory = $loaderFactory;
        $this->dumperFactory = $dumperFactory;
    }

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

    //TODO: convertString - could be convertFile using the temp stream?
}
