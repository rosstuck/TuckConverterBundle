<?php
namespace Tuck\ConverterBundle\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tuck\ConverterBundle\Exception\UnknownFormatException;

/**
 * Creates config loaders based on a particular format
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class LoaderFactory
{
    protected $shortTypeToClassMapping = array(
        'xml' => 'Symfony\Component\DependencyInjection\Loader\XmlFileLoader',
        'yaml' => 'Symfony\Component\DependencyInjection\Loader\YamlFileLoader',
        'yml' => 'Symfony\Component\DependencyInjection\Loader\YamlFileLoader',
        'php' => 'Symfony\Component\DependencyInjection\Loader\PhpFileLoader',
        'ini' => 'Symfony\Component\DependencyInjection\Loader\IniFileLoader'
    );

    /**
     * Creates a loader for the service config file
     *
     * @param  string           $type      The name of the file type, such as xml, yml or php
     * @param  ContainerBuilder $container
     * @param  string           $path      The path to the *directory* containing the the file
     * @return mixed
     */
    public function createFileLoader($type, ContainerBuilder $container, $path)
    {
        $className = $this->getClassNameByShortType($type);

        return new $className($container, new FileLocator($path));
    }

    /**
     * Get loader class name based on short name
     * @param  string                 $type
     * @return string                 Loader class name
     * @throws UnknownFormatException
     */
    protected function getClassNameByShortType($type)
    {
        if (!isset($this->shortTypeToClassMapping[$type])) {
            throw UnknownFormatException::create($type);
        }

        return $this->shortTypeToClassMapping[$type];
    }
}
