<?php

namespace LightWeightFramework;

use LightWeightFramework\Container\Container;
use LightWeightFramework\Exception\OutputBufferException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\Routing\Router;

class LightWeightFramework
{
    public function __construct()
    {
        $this->configure();
    }

    public function configure(string $path = __DIR__ . '/../src/configure.php'): void
    {
        if (file_exists($path)) {
            include $path;
        }
    }

    public function handle(?Request $request = null): Response
    {
        Container::build();
        $request = $request ?? Request::createFromGlobals();
        $route = Router::resolve($request);

        // No route found > Too bad
        if (\is_null($route)) {
            return new Response("No route found", 404);
        }

        // Route has a callback > call it
        if (\is_callable($route->callback)) {
            return call_user_func($route->callback);
        }

        // Route has a method > Try to use it as a procedural script
        if (\is_string($route->method)) {
            $responsePath = __DIR__ . "/../" . $route->method;

            if (file_exists($responsePath)) {
                ob_start();
                $output = require $responsePath;
                $content = ob_get_clean();

                if (\is_string($output)) {
                    $output = new Response($output);
                }
                if ($output instanceof Response) {
                    return $output;
                }
                if (\is_string($content) && $content !== "") {
                    return new Response($content);
                }
                throw new OutputBufferException("Unrecoverable output buffer error");
            }
        }

        // Router returned an unexpected route type > Throw exception
        return new Response("Couldn't handle request", 404);
    }

    public static function getContainer(): Container
    {
        return Container::getInstance();
    }
}
