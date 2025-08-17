<?php

namespace LightWeightFramework\Routing;

use LightWeightFramework\Exception\RouteCollectionGenerationException;
use LightWeightFramework\Http\Request\Request;

class Router
{
    /**
     * @param Request $request
     * @return Route|null
     *
     */
    public static function resolve(Request $request): ?Route
    {
        try {
            $routes = RouteCollection::getRoutes();
        } catch (RouteCollectionGenerationException $e) {
            return null;
        }

        foreach ($routes as $route) {
            if ($route->match($request)) {
                return $route;
            }
        }

        return null;
    }
}
