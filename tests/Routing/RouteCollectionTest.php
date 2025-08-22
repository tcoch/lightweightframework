<?php

namespace Routing;

use LightWeightFramework\Exception\RouteCollectionGenerationException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Routing\Route;
use LightWeightFramework\Routing\RouteCollection;
use LightWeightFramework\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouteCollectionTest extends TestCase
{
    public function testDirectRoutingFolderNotExisting(): void
    {
        $this->expectException(RouteCollectionGenerationException::class);
        RouteCollection::registerPathForDirectRouting('fakePath');
    }

    public function testAddDirectRoutingOnServices(): void
    {
        RouteCollection::registerPathForDirectRouting('Service');

        $routes = RouteCollection::getInstance()->getRoutes();
        $paths = array_map(function (Route $route) { return $route->path; }, $routes);
        self::assertContains('/Service/ServiceA.php', $paths);
        self::assertContains('/Service/SubService/SubServiceA.php', $paths);
        self::assertNotContains('/Service/DummyService', $paths);
    }
}
