<?php

namespace Klepak\RestClient\Tests\Unit;

use Illuminate\Support\Collection;
use Klepak\RestClient\Clients\RestClient;
use Klepak\RestClient\Tests\TestCase;

class RestClientTest extends TestCase
{
    public function testCanGetBasicData()
    {
        $response = $this->getClient()->get('/todos/1');

        $this->assertInstanceOf(
            Collection::class,
            $response->data
        );

        $this->assertNotEmpty(
            $response->data
        );
    }

    public function testCanResetFilterAfterSuccessfulRequest()
    {
        $client = $this->getClient();

        $client->filter('test');

        $this->assertEquals('test', $client->filter);

        $client->get('/todos/1');

        $this->assertNull($client->filter);
    }

    public function testCanSerializeBasicData()
    {
        $client = $this->getClient();

        $response = $client->get('/todos');

        $this->assertInstanceOf(
            TestModel::class,
            $response->asModel(TestModel::class)->models()->first()
        );
    }

    public function getClient()
    {
        return new RestClient('https://jsonplaceholder.typicode.com');
    }
}

class TestModel
{

}
