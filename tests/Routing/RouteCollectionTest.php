<?php

namespace Routing;

use LightWeightFramework\Exception\RouteCollectionGenerationException;
use LightWeightFramework\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

/**
 * This test renames the routing file router.php before running test (that will fail on purpose)
 */
class RouteCollectionTest extends TestCase
{
    public function setUp(): void
    {
        rename(__DIR__ . "/../../src/router.php", __DIR__ . "/../../src/routing.php");
    }

    public function testRouterDefinitionNotFound(): void
    {
        RouteCollection::clear();
        $this->expectException(RouteCollectionGenerationException::class);
        RouteCollection::getRoutes();
    }

    public function tearDown(): void
    {
        rename(__DIR__ . "/../../src/routing.php", __DIR__ . "/../../src/router.php");
    }
}
