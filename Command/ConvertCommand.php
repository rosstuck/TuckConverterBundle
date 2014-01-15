<?php
namespace Tuck\ConverterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tuck\ConverterBundle\Exception\UnknownBundleException;
use Tuck\ConverterBundle\Exception\EmptyDirectoryException;

/**
 * Converts a service config file to another format.
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class ConvertCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('container:convert')
            ->setDescription('Convert a service container config from one format to another')
            ->addArgument('format', InputArgument::REQUIRED, 'The format to convert to (yml, xml, graphviz, php)')
            ->addArgument('file', InputArgument::OPTIONAL, 'Source file to convert')
            ->addOption('output', 'o', InputOption::VALUE_NONE, 'Echo the new config instead of writing it to a file');
    }

    /**
     * Interactively determine the file and convert it
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileToConvert = $this->determineFile($output, $input->getArgument('file'));
        $newFormat = $input->getArgument('format');

        $newConfig = $this
            ->getContainer()
            ->get('tuck_converter.config_format_converter')
            ->convertFile($fileToConvert, $newFormat);

        if ($input->getOption('output')) {
            $output->write($newConfig);
        } else {
            $this->writeToFile($output, $fileToConvert, $newConfig, $newFormat);
        }
    }

    /**
     * Choose file from cli input or interactively
     *
     * @param  OutputInterface $output
     * @param  string          $givenFileName
     * @return SplFileInfo
     */
    protected function determineFile(OutputInterface $output, $givenFileName)
    {
        if (is_file($givenFileName)) {
            return new \SplFileInfo($givenFileName);
        }

        return $this->chooseFileInDirectory(
            $output,
            $this->chooseBundle($output)->getPath().'/Resources/config/'
        );
    }

    /**
     * Prompt the user to choose the bundle whose config they want to convert
     *
     * @param  OutputInterface        $output
     * @return Bundle
     * @throws UnknownBundleException
     */
    protected function chooseBundle(OutputInterface $output)
    {
        $bundles = $this->getContainer()->get('kernel')->getBundles();

        $selectedBundle = $this->getHelperSet()->get('dialog')->ask(
            $output,
            '<info>Which bundle\'s config would you like to convert? </info>',
            null,
            array_keys($bundles)
        );

        if (!isset($bundles[$selectedBundle])) {
            throw UnknownBundleException::create($selectedBundle);
        }

        return $bundles[$selectedBundle];
    }

    /**
     *
     * @param  OutputInterface         $output
     * @param  string                  $path
     * @return SplFileInfo
     * @throws EmptyDirectoryException
     */
    protected function chooseFileInDirectory(OutputInterface $output, $path)
    {
        // Gather files in directory
        $files = array();
        foreach (new \FilesystemIterator($path) as $file) {
            $files[] = $file;
        }

        // Handle edge cases
        if (count($files) === 0) {
            throw EmptyDirectoryException::create($path);
        } elseif (count($files) === 1) {
            return current($files);
        }

        // Prompt user for which file
        $fileIndex = $this->getHelperSet()->get('dialog')->select(
            $output,
            '<info>Which file should be converted? </info>',
            array_map(
                function ($fileInfo) {
                    return $fileInfo->getFilename();
                },
                $files
            )
        );

        return $files[$fileIndex];
    }

    /**
     * Write the new config to the same directory as the original file
     *
     * @param OutputInterface $output
     * @param SplFileInfo     $originalFile
     * @param string          $newConfig
     * @param string          $newFormat    The short file name given, like
     */
    protected function writeToFile(OutputInterface $output, SplFileInfo $originalFile, $newConfig, $newFormat)
    {
        $proposedLocation = $originalFile->getPath().'/services.'.$newFormat;
        $location = $this->getHelperSet()->get('dialog')->ask(
            $output,
            "<info>Where should the new config be written?</info> [{$proposedLocation}] ",
            $proposedLocation
        );

        if (file_exists($location)
            && !$this->getHelperSet()->get('dialog')->askConfirmation($output, 'File exists. Overwrite? ')
        ) {
            $output->writeln('Aborting...');

            return;
        }

        file_put_contents($location, $newConfig);
        $output->writeln(
            "Written! Don't forget to update the DependencyInjection/*Extension class in your bundle to use the new ".
            "loader class and delete the old config file."
        );
    }
}
