<?php

use App\Controller\EmptyClass;
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
    $response = new LightWeightFramework()->handle();
    $response->send();
} catch (\Exception $e) {
    $traces = "";
    foreach ($e->getTrace() as $trace) {
        $traces .= $trace["file"] . ": " . $trace["line"] . ($trace[1] ?? "") . "<br>";
    }
    $response = new Response(sprintf("%s<br><br>%s", $traces, $e->getMessage()), 404);
    $response->send();
}
