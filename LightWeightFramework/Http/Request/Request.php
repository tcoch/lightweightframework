<?php

namespace LightWeightFramework\Http\Request;

class Request
{
    public static ?Request $request = null;
    private ServerGlobalVar $requestGlobalVar;

    public static function createFromGlobals(): Request
    {
        if (self::$request === null) {
            self::$request = new self();
            self::$request->requestGlobalVar = new ServerGlobalVar();
        }

        return self::$request;
    }

    public function getRequestUri(): string
    {
        return explode('?', $this->requestGlobalVar->getRequestUri())[0];
    }

    public function setRequestUri(string $uri): Request
    {
        $this->requestGlobalVar->setRequestUri($uri);

        return $this;
    }
}
