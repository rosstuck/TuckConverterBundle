<?php
namespace Tuck\ConverterBundle\Dumper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\DumperInterface;
use Tuck\ConverterBundle\Exception\UnknownFormatException;

/**
 * Factory for Symfony's standard set of Dumpers
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class StandardDumperFactory implements DumperFactory
{
    /**
     * @var array
     */
    protected $shortTypeToClassMapping = array(
        'xml' => 'Symfony\Component\DependencyInjection\Dumper\XmlDumper',
        'yaml' => 'Symfony\Component\DependencyInjection\Dumper\YamlDumper',
        'yml' => 'Symfony\Component\DependencyInjection\Dumper\YamlDumper',
        'php' => 'Symfony\Component\DependencyInjection\Dumper\PhpDumper',
        'gv' => 'Symfony\Component\DependencyInjection\Dumper\GraphvizDumper',
    );

    /**
     * Creates a dumper for use only by the given container
     *
     * @param  string           $type
     * @param  ContainerBuilder $container
     * @return DumperInterface
     */
    public function createDumper($type, ContainerBuilder $container)
    {
        $className = $this->getClassNameByShortType($type);

        return new $className($container);
    }

    /**
     * Get class name based on format's short name
     * @param $type
     * @return mixed
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
