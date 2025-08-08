<?php

namespace LightWeightFramework;

use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\Routing\Router;

class LightWeightFramework
{
    public function handle(?Request $request = null): Response
    {
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
            $responsePath = __DIR__ . "/../src/Controller/" . $route->method;

            // File must exist
            // File must not be a class
            if (file_exists($responsePath) && !$this->classExists("App\\Controller\\" . str_replace(".php", "", $route->method))) {
                ob_start();
                require $responsePath;
                if (!$content = ob_get_clean()) {
                    return new Response("Output buffer error", 404);
                }
                return new Response($content);
            }
        }

        // Router returned an unexpected route type > Throw exception
        return new Response("Couldn't handle request", 404);
    }

    /**
     * Checks if a class exists. If a procedural script is given, any output it produces is ignored.
     * @param string $class
     * @return bool
     */
    private function classExists(string $class): bool
    {
        ob_start();
        $exists = class_exists($class); // Requires autoloading enabled
        ob_end_clean();

        return $exists;
    }
}
