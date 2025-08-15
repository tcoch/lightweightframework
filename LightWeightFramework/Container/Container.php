<?php

namespace LightWeightFramework\Container;

use LightWeightFramework\Exception\ServiceNotFoundException;

class Container
{
    private static ?Container $container = null;

    /** @var callable[] $services */
    private array $services = [];

    private function __construct() {}

    public static function build(): void
    {
        $instance = self::getInstance();
        $instance->autoRegisterFramework();
        $instance->autoRegisterServices();
    }

    public static function getInstance(): Container
    {
        if (null === self::$container) {
            self::$container = new self();
            self::build();
        }

        return self::$container;
    }

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

    private function autoRegisterServices(): void
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

    /**
     * Automatically adds every framework related classes to the container
     * @param string $folder Folder where to check for existing classes
     * @return void
     */
    private function autoRegisterFramework(string $folder = __DIR__ . '/../'): void
    {
        foreach (scandir($folder) as $fileName) {
            $className = str_replace(".php", "", $fileName);

            if ('.' !== $fileName && '..' !== $fileName && \is_dir($folder . $fileName)) {
                $this->autoRegisterFramework($folder . $fileName . '/');
            }

            $realpath = realpath($folder);
            $classPath = strstr($realpath, 'LightWeightFramework');
            $className = str_replace('/', '\\', $classPath) . '\\' . $className;
            if (class_exists($className) && str_ends_with($folder . $fileName, '.php')) {
                $this->set($className, function () use ($className) { return new $className(); });
            }
        }
    }
}
