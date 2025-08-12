<?php

namespace LightWeightFramework\Http\Request;

class Request
{
    public static ?Request $request = null;
    private ServerGlobalVar $requestGlobalVar;
    private GetGlobalVar $getGlobalVar;

    public static function createFromGlobals(): Request
    {
        if (self::$request === null) {
            self::$request = new self();
            self::$request->requestGlobalVar = new ServerGlobalVar();
            self::$request->getGlobalVar = new GetGlobalVar();
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
        $this->defineQueryString($uri);

        return $this;
    }

    private function defineQueryString(string $queryString): self
    {
        $this->getGlobalVar->setQueryString($queryString);

        return $this;
    }
}
