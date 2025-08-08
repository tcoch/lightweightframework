<?php

namespace LightWeightFramework\Routing;

use LightWeightFramework\Http\Request\Request;

class Route
{
    public function __construct(string $path, mixed $callback) {
        $this->path = $path;
        $this->assignCallback($callback);
    }

    public string $name;

    public string $path;

    public mixed $class;

    public ?string $method = null;

    public mixed $callback = null;

    public function match(Request $request) : bool {
        return $this->path === $request->getRequestUri();
    }

    /**
     * If $callback is callable, assigns the callback.
     * If it's just a string, assigns it to the $method attribute to be called as a procedural script
     * @param mixed $callback
     * @return void
     */
    private function assignCallback(mixed $callback): void
    {
        if (\is_callable($callback)) {
            $this->callback = $callback;
            return;
        }

        if (\is_string($callback)) {
            $this->method = $callback;
            return;
        }
    }
}
