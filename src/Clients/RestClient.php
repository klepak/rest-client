<?php

namespace Klepak\RestClient\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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

        return $route;
    }

    private function getHeaders($headers = [])
    {
        if(!is_null($this->token) && !isset($headers['Authorization']))
            $headers['Authorization'] = $this->token->getType() . ' ' . $this->token->getAccessToken();
    }

    private function getQueryParams($params = [])
    {
        if(!is_null($this->filter) && !isset($params['$filter']))
            $params['$filter'] = $this->filter;
    }

    public function get($route = null)
    {
        $url = $this->baseUri . $this->getRoute($route);

        $client = new Client();

        try
        {
            $response = $client->get($url, [
                'query' => $this->getQueryParams(),
                'headers' => $this->getHeaders()
            ]);

            return new RestClientResponse($route, $response);
        }
        catch(ClientException $clientException)
        {
            throw new RestException(
                (string)$clientException->getResponse()->getBody(),
                $clientException
            );
        }
    }
}
