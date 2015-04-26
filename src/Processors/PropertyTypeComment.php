<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Items;
use Swagger\Annotations\Property;
use Swagger\ClassAnnotations;

class PropertyTypeComment
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
        if (! $annotation->type) {
            $varType   = $this->getVarType($property);
            $isArray   = substr($varType, -2) === '[]';
            $innerType = $isArray ? substr($varType, 0, -2) : $varType;

            $swaggerType = isset(ClassProperties::$types[strtolower($innerType)])
                ? ClassProperties::$types[strtolower($innerType)]
                : null;

            if ($isArray) {
                $container = $annotation->items ?: new Items([]);

                $annotation->type  = 'array';
                $annotation->items = $container;
            } else {
                $container = $annotation;
            }

            if ($swaggerType && is_array($swaggerType)) {
                list($container->type, $container->format) = $swaggerType;
            } elseif ($swaggerType) {
                $container->type = $swaggerType;
            } else {
                $container->ref = '#/definitions/' . $innerType;
            }
        }
    }

    private function getVarType(\ReflectionProperty $property)
    {
        foreach ($this->getCommentRows($property) as $row) {
            if (strpos($row, '@var') === 0) {
                return trim(substr($row, strlen('@var')));
            }
        }

        return null;
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
