<?php

namespace Klepak\RestClient\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
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

    public $filter;

    protected $responseDataKey;

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

    public function response($route, $response)
    {
        return new RestClientResponse($route, $response, $this->responseDataKey);
    }


    public function get($route = null)
    {
        return $this->request('GET', $route);
    }

    public function postJson($route = null, $data)
    {
        return $this->request('POST', $route, [
            RequestOptions::JSON => $data
        ]);
    }

    public function request($method, $route = null, $options = [])
    {
        $url = $this->baseUri . $this->getRoute($route);

        $defaultOptions = [
            'query' => $this->getQueryParams(),
            'headers' => $this->getHeaders()
        ];

        foreach($defaultOptions as $key => $value)
        {
            if(!isset($options[$key]))
                $options[$key] = $value;
        }

        $this->debugLog("$method $url", $options);

        $client = new Client();

        try
        {
            $response = $client->request($method, $url, $options);

            $this->filter = null;

            return $this->response($route, $response);
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
