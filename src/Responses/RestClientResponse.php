<?php

namespace Klepak\RestClient\Responses;

use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Str;
use Klepak\RestClient\Exceptions\ModelSerializerException;

class RestClientResponse
{
    public $rawResponse;

    public $route;
    public $data;

    public $modelName;

    public function __construct(string $route, Response $rawResponse)
    {
        $this->route = $route;
        $this->rawResponse = $rawResponse;

        $contentType = $rawResponse->getHeader('Content-Type');
        $contentType = isset($contentType[0]) ? $contentType[0] : null;

        $body = (string)$rawResponse->getBody();

        if(Str::startsWith($contentType, 'application/json'))
        {
            $this->data = json_decode($body);

            if(!is_array($this->data))
                $this->data = [$this->data];

            $this->data = collect($this->data);
        }
        else
        {
            $this->data = $body;
        }
    }

    private function isRaw()
    {
        return !is_array($this->data);
    }

    public function asModel(string $modelName)
    {
        $this->modelName = $modelName;

        return $this;
    }

    /**
     * serializerCallback: function($item, $model), return $model
     */
    public function models($serializerCallback = null)
    {
        if(is_null($this->modelName))
            throw new ModelSerializerException('No model name defined');

        if(!$this->isRaw())
            throw new ModelSerializerException('Cannot serialize raw response data');

        if(is_null($serializerCallback))
            $serializerCallback = function($item, $model) {
                foreach($item as $key => $value)
                {
                    $model->{$key} = $value;
                }

                return $model;
            };

        $modelName = $this->modelName;

        return $this->data->map(function($item) use ($modelName, $serializerCallback) {
            $model = new $modelName();

            return $serializerCallback($item, $model);
        });
    }
}
