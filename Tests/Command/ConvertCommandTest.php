<?php
namespace Tuck\ConverterBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tuck\ConverterBundle\Command\ConvertCommand;
use Mockery;
use Tuck\ConverterBundle\ConfigFormatConverter;
use Tuck\ConverterBundle\Dumper\StandardDumperFactory;
use Tuck\ConverterBundle\File\SysTempFileFactory;
use Tuck\ConverterBundle\Loader\StandardLoaderFactory;

class ConvertCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCanOutputSimpleMock()
    {
        $commandTester = $this->executeCommand(
            array(
                'format' => 'yml',
                'file' => __DIR__.'/../../Resources/mock_config/simple.xml',
                '--output' => true
            )
        );

        $this->assertStringEqualsFile(
            __DIR__.'/../../Resources/mock_config/simple.yml',
            $commandTester->getDisplay()
        );
    }

    /**
     * Helper to create a command
     * @param  array         $arguments
     * @return CommandTester
     */
    protected function executeCommand(array $arguments)
    {
        // Create the command
        $application = new Application();
        $application->add(new ConvertCommand());
        $command = $application->find('container:convert');

        // Mock the container with a convertor
        $converter = new ConfigFormatConverter(new StandardLoaderFactory(), new StandardDumperFactory(), new SysTempFileFactory());
        $mockContainer = Mockery::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $mockContainer->shouldReceive('get')->with('tuck_converter.config_format_converter')->andReturn($converter);
        $command->setContainer($mockContainer);

        // Configure the tester and return it
        $commandTester = new CommandTester($command);
        $arguments['command'] = $command->getName();
        $commandTester->execute($arguments);

        return $commandTester;
    }
}
