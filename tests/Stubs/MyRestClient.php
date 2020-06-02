<?php

namespace Klepak\RestClient\Tests\Stubs;

use Klepak\RestClient\Clients\RestClient;

class MyRestClient extends RestClient
{
    protected $responseDataKey = 'not-exist';
}