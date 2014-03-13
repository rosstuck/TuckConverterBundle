<?php
namespace Tuck\ConverterBundle\Exception;

/**
 * Failed attempt to create a file where one of the same name already exists
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class FileAlreadyExistsException extends \Exception
{
    /**
     * Factory function to format message
     *
     * @param string $path
     * @return self
     */
    public static function create($path)
    {
        return new static("Attempted to create file at '{$path}' but the file already exists");
    }
}
