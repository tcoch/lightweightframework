<?php

namespace LightWeightFramework\Routing;

use LightWeightFramework\Exception\RouteCollectionGenerationException;

class RouteCollection
{
    /** @var Route[] $routeCollection  */
    private static ?array $routeCollection = null;

    private function __construct()
    {
    }

    /**
     * Used only for testing purposes
     * @internal
     * @return void
     */
    public static function clear(): void
    {
        self::$routeCollection = null;
    }

    /**
     * @return Route[]
     * @throws RouteCollectionGenerationException
     */
    public static function getRoutes(): array
    {
        if (null === self::$routeCollection) {
            self::$routeCollection = new self()->defineRoutes();
        }

        return self::$routeCollection;
    }

    /**
     * @return Route[]
     * @throws RouteCollectionGenerationException
     */
    private function defineRoutes(): array
    {
        $routes = array_merge($this->readRoutesDefinition(), $this->createRouteForProceduralScripts());

        $routeCollection = [];
        foreach ($routes as $path => $callback) {
            $routeCollection[] = new Route($path, $callback);
        }

        return $routeCollection;
    }

    /**
     * Reads routes defined in src/router.php
     * @return string[]
     * @throws RouteCollectionGenerationException
     */
    private function readRoutesDefinition(): array
    {
        if (file_exists($basefile = __DIR__ . '/../../src/router.php')) {
            $baseRoutes = include $basefile;
            return array_merge($baseRoutes, self::$routeCollection ?? []);
        }

        throw new RouteCollectionGenerationException("No routes loaded because file $basefile does not exist");
    }

    /**
     * Automatically creates routes for existing procedural script in `src/Controller` folder
     * @return string[]
     */
    private function createRouteForProceduralScripts(): array
    {
        $controllerFolder = __DIR__ . '/../../src/Controller/';

        $routes = [];
        foreach (scandir($controllerFolder) as $fileName) {
            // Handle only PHP files, that are not associated to a class
            if (str_ends_with($controllerFolder . $fileName, '.php')
                && !class_exists("\\App\\Controller\\" . str_replace(".php", "", $fileName), false)
            ) {
                $routes["/$fileName"] = $fileName;
            }
        }

        return $routes;
    }

    public static function addRoute(Route $route): void
    {
        if (null === self::$routeCollection) {
            self::$routeCollection = [];
        }

        self::$routeCollection[] = $route;
    }
}
