<?php

namespace LightWeightFramework\Container\Register;

use LightWeightFramework\Container\Container;

class ServiceRegister implements ContainerRegisterInterface
{
    /**
     * Automatically adds every classes in src/Service folder (if exists)
     * @param string $folder Folder where to check for existing classes
     * @return void
     */
    public function register(string $folder = __DIR__ . '/../../../src/Service/'): void
    {
        // Autowiring of services
        foreach (scandir($folder) as $fileName) {
            $className = str_replace(".php", "", $fileName);

            if ('.' !== $fileName && '..' !== $fileName && \is_dir($folder . $fileName)) {
                $this->register($folder . $fileName . '/');
            }

            $realpath = realpath($folder);
            $classPath = strstr($realpath, 'src');
            $className = str_replace(['src', '/'], ['App', '\\'], $classPath) . '\\' . $className;

            // Handle only PHP files, that are associated to a class
            if (class_exists($className) && str_ends_with($folder . $fileName, '.php')) {
                Container::getInstance()->set($className, function () use ($className) { return new $className(); });
            }
        }
    }

    public function supportsAutoloading(string $folder = __DIR__ . '/../../../src/Service/'): bool
    {
        return is_dir($folder);
    }
}
