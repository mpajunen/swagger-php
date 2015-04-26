<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\AbstractAnnotation;
use Swagger\Annotations\Definition;
use Swagger\ClassAnnotations;

class DefinitionName
{
    public function __invoke(ClassAnnotations $annotations)
    {
        if (! $annotations->classAnnotations) {
            return;
        }

        foreach ($annotations->classAnnotations->getSwaggerAnnotations() as $annotation) {
            $this->processAnnotation($annotations->classAnnotations->target, $annotation);
        }
    }

    private function processAnnotation(\ReflectionClass $class, AbstractAnnotation $annotation)
    {
        if ($annotation instanceof Definition && ! $annotation->definition) {
            $annotation->definition = $class->getShortName();
        }
    }
}
