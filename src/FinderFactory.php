<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Exception;
use Symfony\Component\Finder\Finder;

class FinderFactory
{
    /**
     * @param string|array|Finder $source
     * @param string|array        $exclude
     *
     * @return Finder
     * @throws Exception
     */
    public static function getFinder($source, $exclude = [])
    {
        $finder = is_object($source) ? $source : self::createFinder($source);
        $finder->exclude($exclude);

        return $finder;
    }

    private static function createFinder($directory)
    {
        $finder = new Finder();
        $finder->files();
        if (is_string($directory)) {
            if (is_file($directory)) { // Scan a single file?
                $finder->append([$directory]);
            } else { // Scan a directory
                $finder->in($directory);
            }
        } elseif (is_array($directory)) {
            foreach ($directory as $path) {
                if (is_file($path)) { // Scan a file?
                    $finder->append([$path]);
                } else {
                    $finder->in($path);
                }
            }
        } else {
            throw new \Exception('Unexpected $directory value: ' . gettype($directory));
        }

        return $finder;
    }
}
