<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Doctrine\Common\Annotations\AnnotationReader;
use Swagger\Processors\DefinitionName;
use Swagger\Processors\PropertyDescriptionComment;
use Swagger\Processors\PropertyName;
use Swagger\Processors\PropertyTypeComment;

class Reader
{
    /** @var AnnotationReader */
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return array|mixed
     */
    public function parseClass(\ReflectionClass $class)
    {
        $createMethod = function (\ReflectionMethod $method) {
            return new AnnotationContainer($method, $this->annotationReader->getMethodAnnotations($method));
        };
        $createProperty = function (\ReflectionProperty $property) {
            return new AnnotationContainer($property, $this->annotationReader->getPropertyAnnotations($property));
        };

        $annotations = new ClassAnnotations(
            new AnnotationContainer($class, $this->annotationReader->getClassAnnotations($class)),
            array_map($createMethod, $class->getMethods()),
            array_map($createProperty, $class->getProperties())
        );

        $processors = [
            new DefinitionName(),
            new PropertyDescriptionComment(),
            new PropertyName(),
            new PropertyTypeComment(),
        ];

        foreach ($processors as $processor) {
            $processor($annotations);
        }

        return $annotations->getAnnotations();
    }
}
