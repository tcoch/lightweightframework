<?php

namespace LightWeightFramework\Routing;

use LightWeightFramework\Exception\RouteCollectionGenerationException;

class RouteCollection
{
    private static ?RouteCollection $instance = null;

    /** @var Route[] $routes  */
    private array $routes = [];

    private function __construct()
    {
        $this->readRoutesDefinition();
    }

    public static function getInstance(): RouteCollection
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::registerPathForDirectRouting('Controller');
        }

        return self::$instance;
    }

    /**
     * Reads routes defined in src/router.php
     */
    private function readRoutesDefinition(): void
    {
        if (file_exists($basefile = __DIR__ . '/../../src/router.php')) {
            $routes = include $basefile;

            foreach ($routes as $path => $callback) {
                if (\is_string($callback)) {
                    $this->addRoute(new Route($path, 'src/Controller/' . $callback));
                } else {
                    $this->addRoute(new Route($path, $callback));
                }
            }
        }
    }

    public function addRoute(Route $route): void
    {
        if (!\array_key_exists($route->path, $this->routes)) {
            $this->routes[$route->path] = $route;
        }
    }

    /**
     * @param string $path Must be part of the src/ folder
     * @return void
     * @throws RouteCollectionGenerationException
     */
    public static function registerPathForDirectRouting(string $path): void
    {
        $path = __DIR__ . '/../../src/' . $path;
        if (!\is_dir($path)) {
            throw new RouteCollectionGenerationException("Path $path not found");
        }

        $routes = [];
        $path = realpath($path);
        foreach (scandir($path) as $fileName) {
            $pathFromSrc = strstr($path, 'src');
            if ('.' !== $fileName && '..' !== $fileName && \is_dir($path . '/' . $fileName)) {
                self::registerPathForDirectRouting(str_replace('src/', '', $pathFromSrc) . '/' . $fileName);
            }

            // Handle only PHP files, that are not associated to a class
            if (str_ends_with($pathFromSrc . '/' . $fileName, '.php')) {
                $routeFullPathStartingFromSrc = $pathFromSrc . '/' . $fileName;
                $routePath = str_replace(['src/Controller/', 'src/'], '', $pathFromSrc . '/' . $fileName);

                $routes['/' . $routePath] = $routeFullPathStartingFromSrc;
            }
        }

        foreach ($routes as $routePath => $callback) {
            self::getInstance()->addRoute(new Route($routePath, $callback));
        }
    }

    /**
     * @return null|Route[]
     */
    public function getRoutes(): ?array
    {
        return $this->routes;
    }
}
