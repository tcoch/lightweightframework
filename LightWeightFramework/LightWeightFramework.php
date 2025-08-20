<?php

namespace LightWeightFramework;

use LightWeightFramework\Container\Container;
use LightWeightFramework\Exception\OutputBufferException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\Routing\Router;

class LightWeightFramework
{
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
            $responsePath = __DIR__ . "/../src/Controller/" . $route->method;

            if (file_exists($responsePath)) {
                ob_start();
                $output = require $responsePath;

                if (!$content = ob_get_clean()) {
                    if ($output instanceof Response) {
                        return $output;
                    }
                    if (\is_string($output)) {
                        return new Response($output);
                    }
                    throw new OutputBufferException("Unrecoverable output buffer error");
                }
                return new Response($content);
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
