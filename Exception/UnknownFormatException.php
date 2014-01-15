<?php
/**
 * Created by PhpStorm.
 * User: rtuck
 * Date: 1/8/14
 * Time: 9:27 AM
 */

namespace Tuck\ConverterBundle\Exception;

/**
 * Thrown when receiving an unknown file format for an operation
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
class UnknownFormatException extends \Exception
{
    /**
     * Factory to format message
     *
     * @param  string $type
     * @return self
     */
    public static function create($type)
    {
        return new static("No adapter found for format '{$type}'");
    }
}
