<?php

namespace LightWeightFramework\Container;

use LightWeightFramework\Container\Register\ContainerRegisterInterface;
use LightWeightFramework\Exception\ServiceNotFoundException;

class Container
{
    private static ?Container $container = null;

    /** @var callable[] $services */
    private array $services = [];

    /** @var ContainerRegisterInterface[] $registerers */
    private array $registerers = [];

    private bool $built = false;

    private function __construct() {}

    /**
     * Registers every class that implements ContainerRegisterInterface to handle the related autoloading
     * @return void
     */
    private function registerAutoloaders(): void
    {
        foreach (get_declared_classes() as $class) {
            if (is_a($class, ContainerRegisterInterface::class, true)) {
                $this->registerers[] = new $class;
            }
        }
    }

    public static function build(): void
    {
        $instance = self::getInstance();
        if ($instance->built) {
            return;
        }

        $instance->registerFramework();

        $instance->registerAutoloaders();
        foreach ($instance->registerers as $registerer) {
            if ($registerer->supportsAutoloading()) {
                $registerer->register();
            }
        }

        $instance->built = true;
    }

    /**
     * Automatically adds every framework related classes to the container
     * @param string $folder Folder where to check for existing classes
     * @return void
     */
    private function registerFramework(string $folder = __DIR__ . '/../'): void
    {
        foreach (scandir($folder) as $fileName) {
            $className = str_replace(".php", "", $fileName);

            if ('.' !== $fileName && '..' !== $fileName && \is_dir($folder . $fileName)) {
                $this->registerFramework($folder . $fileName . '/');
            }

            $realpath = realpath($folder);
            $classPath = strstr($realpath, 'LightWeightFramework');
            $className = str_replace('/', '\\', $classPath) . '\\' . $className;

            if (class_exists($className) && str_ends_with($folder . $fileName, '.php')) {
                self::getInstance()->set($className, function () use ($className) { return new $className(); });
            }
        }
    }

    public static function getInstance(): Container
    {
        if (null === self::$container) {
            self::$container = new self();
            self::build();
        }

        return self::$container;
    }

    public function set(string $id, callable $callable): self
    {
        if (!\array_key_exists($id, $this->services)) {
            $this->services[$id] = $callable;
        }

        return $this;
    }

    public function get(string $id): mixed
    {
        if (!isset($this->services[$id])) {
            throw new ServiceNotFoundException("Service '$id' doesn't exist.");
        }

        $callable = $this->services[$id];

        return $callable($this);
    }
}
