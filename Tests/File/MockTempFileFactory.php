<?php

namespace Tuck\ConverterBundle\Tests\File;

use Tuck\ConverterBundle\File\SysTempFileFactory;

/**
 * Always creates a temp file with the same name.
 *
 * Useful for testing for naming collisions when generating random file names
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class MockSysTempFileFactory extends SysTempFileFactory
{
    /**
     * Always returns the same file name (provided the same extension is given)
     *
     * @param string $extension
     * @return string
     */
    protected function generateFilename($extension)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tuck_converter_bundle_test_file.'.$extension;
    }

    /**
     * Make the file name accessible so we can use it in unit tests
     *
     * @param string $extension
     * @return string
     */
    public function getMockFilename($extension)
    {
        return $this->generateFilename($extension);
    }
}