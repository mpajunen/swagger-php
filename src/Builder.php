<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Doctrine\Common\Annotations\TokenParser;
use Swagger\Annotations\Swagger;
use Swagger\Processors\BuildPaths;
use Symfony\Component\Finder\Finder;

class Builder
{
    public function parse($directory, $exclude = [])
    {
        $finder  = FinderFactory::getFinder($directory, $exclude);
        $swagger = new Swagger([]);

        foreach ($this->getAnnotations($finder) as $classAnnotations) {
            foreach ($classAnnotations as $annotation) {
                if ($annotation instanceof Swagger) {
                    $swagger->mergeProperties($annotation);
                } else {
                    $swagger->merge([$annotation]);
                }
            }
        }

        $processors = [
            new BuildPaths(),
        ];

        foreach ($processors as $processor) {
            $processor($swagger);
        }

        $swagger->validate();

        return $swagger;
    }

    private function getAnnotations(Finder $finder)
    {
        $annotations = [];
        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $class = $this->getClassName($file);
            if ($class === null) {
                continue;
            }

            $annotations[] = (new Reader())->parseClass(new \ReflectionClass($class));
        }

        return $annotations;
    }

    private function getClassName(\SplFileInfo $file)
    {
        $parser = new TokenParser(file_get_contents($file->getPathname()));

        while ($token = $parser->next()) {
            if ($token[0] === T_NAMESPACE) {
                $namespace = $parser->parseNamespace();
            } elseif ($token[0] === T_CLASS) {
                $class = $parser->parseClass();
                break;
            }
        }

        return isset($class) ? (isset($namespace) ? $namespace . '\\' . $class : $class) : null;
    }
}
