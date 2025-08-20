<?php

namespace LightWeightFramework\Http\Request;

use LightWeightFramework\Superglobals\Get;
use LightWeightFramework\Superglobals\Server;

class Request
{
    public static ?Request $request = null;
    private Server $requestGlobalVar;
    private Get $getGlobalVar;

    public static function createFromGlobals(): Request
    {
        if (self::$request === null) {
            self::$request = new self();
            self::$request->requestGlobalVar = new Server();
            self::$request->getGlobalVar = new Get();
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

    public function getRequestMethod(): string
    {
        return $this->requestGlobalVar->getRequestMethod();
    }

    public function setRequestMethod(string $httpMethod): Request
    {
        $this->requestGlobalVar->setRequestMethod($httpMethod);

        return $this;
    }
}
