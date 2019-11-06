<?php

namespace Klepak\RestClient\Tests\Unit;

use Klepak\RestClient\Clients\RestClient;
use Klepak\RestClient\Tests\TestCase;

class RestClientTest extends TestCase
{
    public function testStuff()
    {
        $client = new RestClient('https://jsonplaceholder.typicode.com');

        $response = $client->get('/todos/1');

        dd($response);
    }
}
