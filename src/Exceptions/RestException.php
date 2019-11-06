<?php

namespace Klepak\RestClient\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Exception;

class RestException extends Exception
{
    public $clientException;

    public function __construct(string $message, ClientException $clientException)
    {
        $this->clientException = $clientException;

        if(empty($message) || $message == '{}')
            $message = $clientException->getMessage();

        parent::__construct($message);
    }
}
