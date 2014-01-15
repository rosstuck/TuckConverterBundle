<?php
namespace Tuck\ConverterBundle\Exception;

/**
 * Thrown when no files were found in a specified directory
 * @author Ross Tuck <me@rosstuck.com>
 */
class EmptyDirectoryException extends \Exception
{
    /**
     * Factory function to format message
     * @param  string $path
     * @return self
     */
    public static function create($path)
    {
        return new static("No files were found in directory '{$path}'");
    }
}
