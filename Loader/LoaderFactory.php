<?php
namespace Tuck\ConverterBundle\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Creates config loaders based on a particular format
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface LoaderFactory
{
    /**
     * Creates a loader for the service config file
     *
     * @param  string           $type      The name of the file type, such as xml, yml or php
     * @param  ContainerBuilder $container
     * @param  string           $path      The path to the *directory* containing the the file
     * @return LoaderInterface
     */
    public function createFileLoader($type, ContainerBuilder $container, $path);
}
