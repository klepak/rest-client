<?php

namespace Klepak\RestClient\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Klepak\RestClient\Exceptions\MissingRouteException;
use Klepak\RestClient\Exceptions\RestException;
use Klepak\RestClient\Interfaces\TokenInterface;
use Klepak\RestClient\Responses\RestClientResponse;

class RestClient
{
    private $baseUri;
    private $token;
    private $route;

    private $filter;

    private $debug = false;

    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    public function token(TokenInterface $token)
    {
        $this->token = $token;

        return $this;
    }

    public function filter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function route($route)
    {
        $this->route = $route;

        return $this;
    }

    private function getRoute($route)
    {
        if(is_null($route))
            $route = $this->route;

        if(is_null($route))
            throw new MissingRouteException();

        if(!Str::startsWith($route, '/'))
            $route = '/'.$route;

        return $route;
    }

    private function getHeaders($headers = [])
    {
        if(!is_null($this->token) && !isset($headers['Authorization']))
            $headers['Authorization'] = 'Bearer ' . $this->token->getAccessToken();

        return $headers;
    }

    private function getQueryParams($params = [])
    {
        if(!is_null($this->filter) && !isset($params['$filter']))
            $params['$filter'] = $this->filter;

        return $params;
    }

    public function get($route = null)
    {
        $url = $this->baseUri . $this->getRoute($route);

        $options = [
            'query' => $this->getQueryParams(),
            'headers' => $this->getHeaders()
        ];

        $this->debugLog("GET $url", $options);

        $client = new Client();

        try
        {
            $response = $client->get($url, $options);

            return new RestClientResponse($route, $response);
        }
        catch(ClientException $clientException)
        {
            $response = $clientException->getResponse();

            throw new RestException(
                "{$response->getStatusCode()} {$response->getReasonPhrase()}: " . (string)$response->getBody(),
                $clientException
            );
        }
    }

    public function debug(bool $debug)
    {
        $this->debug = $debug;

        return $this;
    }

    private function debugLog(string $message, $context = null)
    {
        if($this->debug)
            Log::debug($message, $context);
    }
}
