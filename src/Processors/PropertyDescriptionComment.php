<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\ClassAnnotations;

class PropertyDescriptionComment
{
    public function __invoke(ClassAnnotations $annotations)
    {
        foreach ($annotations->propertyAnnotations as $property) {
            foreach ($property->getSwaggerAnnotations() as $annotation) {
                $this->processAnnotation($property->target, $annotation);
            }
        }
    }

    private function processAnnotation(\ReflectionProperty $property, Property $annotation)
    {
        if (! $annotation->description) {
            $annotation->description = $this->getDescription($property);
        }
    }

    private function getDescription(\ReflectionProperty $property)
    {
        $rows     = $this->getCommentRows($property);
        $firstRow = reset($rows);

        return strpos($firstRow, '@') !== 0 ? $firstRow : null;
    }

    private function getCommentRows(\ReflectionProperty $property)
    {
        $comment = $property->getDocComment();
        $rawRows = array_map('trim', explode("\n", $comment));

        return count($rawRows) > 1
            ? array_map(function ($row) { return trim(substr($row, 1)); }, array_slice($rawRows, 1, -1))
            : implode(' ', array_slice(explode(' ', $rawRows), 1, -1));
    }
}
