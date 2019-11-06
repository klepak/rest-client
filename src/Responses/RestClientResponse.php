<?php

namespace Klepak\RestClient\Responses;

use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;

class RestClientResponse
{
    public $rawResponse;

    public $route;
    public $data;

    public function __construct(string $route, Response $rawResponse)
    {
        $this->route = $route;
        $this->rawResponse = $rawResponse;

        $contentType = $rawResponse->getHeader('Content-Type');
        $contentType = isset($contentType[0]) ? $contentType[0] : null;

        $body = (string)$rawResponse->getBody();

        if(Str::startsWith($contentType, 'application/json'))
            $this->data = json_decode($body);
        else
            $this->data = $body;
    }
}
