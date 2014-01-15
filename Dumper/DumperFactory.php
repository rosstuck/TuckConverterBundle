<?php
namespace Tuck\ConverterBundle\Dumper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\DumperInterface;
use Tuck\ConverterBundle\Exception\UnknownFormatException;

/**
 * Create a config dumper for different formats
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class DumperFactory
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
