<?php

namespace LightWeightFramework\Http\Response;

class ResponseHeaders
{
    public int $statusCode;

    public ?string $location;

    public function sendHeaders(): void
    {
        switch ($this->statusCode) {
            case 200:
                header("HTTP/1.0 200 OK");
                header("Content-Type: text/html; charset=utf-8");
                break;
            case 302:
                header("HTTP/1.0 302 Found");
                header('Location: ' . $this->location);
                break;
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
        }
    }
}
