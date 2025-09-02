<?php

// Auto-register classes
use LightWeightFramework\LightWeightFramework;
use LightWeightFramework\Routing\RouteCollection;

spl_autoload_register(function ($class) {
    $class = str_replace(['\\', 'App/'], ['/', 'src/'], $class);
    $classFile = __DIR__ . '/../' . $class . '.php';
    if (file_exists($classFile)) {
        require $classFile;
    }
});

// Set error handler : transform warnings to exception
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});
