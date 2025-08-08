<?php

namespace LightWeightFramework;

use LightWeightFramework\Exception\UnprocessableRequestException;
use LightWeightFramework\Http\Request\Request;
use LightWeightFramework\Http\Response\Response;
use LightWeightFramework\Routing\Router;

class LightWeightFramework
{
    /**
     * @throws UnprocessableRequestException
     */
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

            if (file_exists($responsePath)) {
                ob_start();
                require $responsePath;
                if (!$content = ob_get_clean()) {
                    throw new UnprocessableRequestException("Output buffer error");
                }
                return new Response($content);
            }
        }

        // Router returned an unexpected route type > Throw exception
        throw new UnprocessableRequestException("Couldn't handle request");
    }
}
