<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Swagger\Builder;

class BuilderTest extends SwaggerTestCase
{
    public static function setUpBeforeClass()
    {
        $loader = require dirname(__DIR__) . '/vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    public function testBuilderOutput()
    {
        $builder = new Builder();
        $swagger = $builder->parse(__DIR__ . '/Fixtures/Shop');

        $this->assertSame($this->getShopExpectedJson(), (string) $swagger);
    }

    /**
     * @dataProvider getExamples
     */
    public function testExample($exampleDir, $outputFile)
    {
        $swagger = (new Builder())->parse(dirname(__DIR__) . '/Examples/' . $exampleDir);
        $this->assertSwaggerEqualsFile(__DIR__ . '/ExamplesOutput/' . $outputFile, $swagger);
    }

    public function getExamples()
    {
        return [
            ['petstore.swagger.io', 'petstore.swagger.io.json'],
            ['swagger-spec/Petstore', 'petstore.json'],
            ['swagger-spec/PetstoreSimple', 'petstore-simple.json'],
            ['swagger-spec/PetstoreWithExternalDocs', 'petstore-with-external-docs.json'],
        ];
    }

    private function getShopExpectedJson()
    {
        return <<<EOT
{
    "swagger": "2.0",
    "info": {
        "title": "Shop",
        "version": "3.7"
    },
    "host": "example.com",
    "basePath": "/api",
    "schemes": [
        "http"
    ],
    "paths": {
        "/products/{product}": {
            "get": {
                "parameters": [
                    {
                        "name": "product",
                        "in": "path",
                        "description": "Product number",
                        "type": "string"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "Product information",
                        "schema": "$/definitions/Product"
                    }
                }
            }
        },
        "/products": {
            "post": {
                "parameters": [
                    {
                        "name": "product",
                        "in": "body",
                        "schema": "$/definitions/Product"
                    }
                ],
                "responses": {
                    "200": {
                        "description": "Product information",
                        "schema": "$/definitions/Product"
                    }
                }
            }
        }
    },
    "definitions": {
        "Product": {
            "properties": {
                "name": {
                    "type": "string"
                },
                "category": {
                    "type": "string"
                }
            }
        }
    }
}
EOT;
    }
}
