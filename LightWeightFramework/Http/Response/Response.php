<?php

namespace LightWeightFramework\Http\Response;

use LightWeightFramework\Superglobals\Get;
use LightWeightFramework\Superglobals\Server;

class Response
{
    protected string $content = "";

    protected ResponseHeaders $headers;

    public Server $serverGlobalVar;

    public function __construct(string $content = "", int $returnCode = 200)
    {
        $this->content = $content;
        $this->headers = new ResponseHeaders();
        $this->headers->statusCode = $returnCode;
        $this->serverGlobalVar = new Server();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getReturnCode(): int
    {
        return $this->headers->statusCode;
    }

    public function send(): void
    {
        $this->sendHeaders();
        echo $this->content;
    }

    private function sendHeaders(): void
    {
        $this->headers->sendHeaders();
    }

    public function getHeaders(): ResponseHeaders
    {
        return $this->headers;
    }
}
