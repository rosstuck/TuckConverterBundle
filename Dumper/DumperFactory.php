<?php
namespace Tuck\ConverterBundle\Dumper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\DumperInterface;

/**
 * Create a config dumper for different formats
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface DumperFactory
{
    /**
     * Creates a dumper for use only by the given container
     *
     * @param  string           $type
     * @param  ContainerBuilder $container
     * @return DumperInterface
     */
    public function createDumper($type, ContainerBuilder $container);
}
