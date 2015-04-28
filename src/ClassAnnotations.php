<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\AbstractAnnotation;

class ClassAnnotations
{
    /** @var AnnotationContainer */
    public $classAnnotations;
    /** @var AnnotationContainer[] */
    public $methodAnnotations;
    /** @var AnnotationContainer[] */
    public $propertyAnnotations;

    public function __construct($classAnnotations, $methodAnnotations, $propertyAnnotations)
    {
        $this->classAnnotations    = $classAnnotations;
        $this->methodAnnotations   = $methodAnnotations;
        $this->propertyAnnotations = $propertyAnnotations;
    }

    /**
     * @return AbstractAnnotation
     */
    public function getRootAnnotation()
    {
        $annotations = $this->classAnnotations->getSwaggerAnnotations();

        return reset($annotations);
    }

    public function getAnnotations()
    {
        $rootAnnotation = $this->getRootAnnotation();
        $subAnnotations = $this->getSubAnnotations();

        if ($rootAnnotation) {
            // Method and property annotations get merged with the first class annotation.
            $rootAnnotation->merge($subAnnotations);

            return $this->classAnnotations->getSwaggerAnnotations();
        } else {
            return $subAnnotations;
        }
    }

    /**
     * @return array
     */
    private function getSubAnnotations()
    {
        $getAnnotations = function (AnnotationContainer $container) { return $container->getSwaggerAnnotations(); };

        return array_reduce(
            array_merge(
                array_map($getAnnotations, $this->methodAnnotations),
                array_map($getAnnotations, $this->propertyAnnotations)
            ),
            'array_merge',
            []
        );
    }
}
