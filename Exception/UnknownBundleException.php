<?php
namespace Tuck\ConverterBundle\Exception;

/**
 * Thrown when a user specifies a missing bundle
 * @author Ross Tuck <me@rosstuck.com>
 */
class UnknownBundleException extends \Exception
{
    /**
     * Factory that formats the message
     * @param $bundleName
     * @return self
     */
    public static function create($bundleName)
    {
        return new static("Bundle '{$bundleName}' does not exist.");
    }
}
