<?php
namespace Tuck\ConverterBundle\File;

/**
 * Creates temp files in the System tmp dir
 *
 * @author Ross Tuck <me@rosstuck.com>
 */
interface TempFileFactory
{
    /**
     * Essentially, an "improved" tempnam().
     *
     * As compared to the core tempnam, this version allows you to add a file
     * extension, returns an SplFileObject and has a short hand for priming it
     * with some content. If content is written, the file is not rewound before
     * returning.
     *
     * @param  string|null    $content
     * @param  string         $extension
     * @return \SplFileObject
     */
    public function createFile($content = null, $extension = 'tmp');
}
