<?php

namespace Routing;

use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Routing\Route;
use LightWeightFramework\Routing\RouteCollection;
use LightWeightFramework\Routing\Router;
use PHPUnit\Framework\TestCase;

class RoutingTest extends TestCase
{
    public function testAvailableRouteWithCallable(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/testRouting");
        $route = Router::resolve($request);

        self::assertNotNull($route);
        self::assertIsCallable($route->callback);
    }

    public function testAvailableRouteTowardsProceduralScript(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/Procedural");
        $route = Router::resolve($request);

        self::assertNotNull($route);
        self::assertNull($route->callback);
        self::assertIsString($route->method);
    }

    public function testRouteNotFound(): void
    {
        $request = Request::createFromGlobals()->setRequestUri("/UndefinedRoute");
        $route = Router::resolve($request);

        self::assertNull($route);
    }

    public function testAddRouteToCollection(): void
    {
        $routeCollection = RouteCollection::getInstance();
        $route = new Route('dummy_path', 'dummy_callback');
        self::assertNotContains($route, $routeCollection->getRoutes());

        $routeCollection->addRoute($route);
        self::assertContains($route, $routeCollection->getRoutes());
    }
}
