<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Swagger\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
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
