<?php
namespace Tuck\ConverterBundle\Exception;

/**
 * Thrown when receiving a missing or unknown format
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class UnknownFileException extends \Exception
{
    /**
     * Factory to format message
     *
     * @param  string $path
     * @return self
     */
    public static function create($path)
    {
        return new static("Could not find file '{$path}'");
    }
}
