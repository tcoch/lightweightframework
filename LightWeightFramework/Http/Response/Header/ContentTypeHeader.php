<?php

namespace LightWeightFramework\Http\Response\Header;

use LightWeightFramework\Http\Response\ResponseHeaders;

class ContentTypeHeader implements ResponseHeaderInterface
{
    private string $contentType = ResponseHeaders::HEADER_CONTENT_TYPE . ": text/html; charset=utf-8";

    public function determineValue(): void
    {
        foreach (headers_list() as $header) {
            if (\str_contains(strtoupper($header), strtoupper(ResponseHeaders::HEADER_CONTENT_TYPE))) {
                $this->contentType = $header;
            }
        }
    }

    public function send(): void
    {
        header($this->contentType);
    }
}
