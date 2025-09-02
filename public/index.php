<?php

use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\LightWeightFramework;

// Auto-register classes
spl_autoload_register(static function ($class) {
    $class = str_replace(['\\', 'App/'], ['/', 'src/'], $class);
    $classFile = __DIR__ . '/../' . $class . '.php';
    if (file_exists($classFile)) {
        require $classFile;
    }
});

// Set error handler : transform warnings to exception
set_error_handler(static function ($errno, $errstr, $errfile, $errline) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

try {
    $f = new LightWeightFramework();
    $response = $f->handle();
    $response->send();
} catch (\Exception $e) {
    $response = new Response($e->getMessage(), 404);
    $response->send();
}
