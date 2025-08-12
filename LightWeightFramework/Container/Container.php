<?php

namespace LightWeightFramework\Container;

use LightWeightFramework\Exception\ServiceNotFoundException;

class Container
{
    private static ?Container $container = null;

    private function __construct() {
        $this->autowireServices();
    }

    public static function getInstance(): Container
    {
        if (null === self::$container) {
            self::$container = new self();
        }

        return self::$container;
    }

    private array $services = [];

    public function set(string $id, callable $callable): void
    {
        $this->services[$id] = $callable;
    }

    public function get(string $id): mixed
    {
        if (!isset($this->services[$id])) {
            throw new ServiceNotFoundException("Service '$id' doesn't exist.");
        }

        $callable = $this->services[$id];

        return $callable($this);
    }

    public function autowireServices(): void
    {
        // Autowiring of services
        $servicesFolder = __DIR__ . '/../../src/Service/';
        foreach (scandir($servicesFolder) as $fileName) {
            $className = "App\\Service\\" . str_replace(".php", "", $fileName);
            // Handle only PHP files, that are associated to a class
            if (class_exists($className) && str_ends_with($servicesFolder . $fileName, '.php')) {
                $this->set($className, function () use ($className) { return new $className(); });
            }
        }
    }
}
