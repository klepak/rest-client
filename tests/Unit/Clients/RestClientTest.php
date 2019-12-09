<?php

namespace Klepak\RestClient\Tests\Unit;

use Illuminate\Support\Collection;
use Klepak\RestClient\Clients\RestClient;
use Klepak\RestClient\Tests\TestCase;

class RestClientTest extends TestCase
{
    public function testCanPostJson()
    {
        $response = $this->getClient()->postJson('/todos', [
            'title' => 'Test todo',
            'completed' => true
        ]);

        $this->assertEquals(
            'Test todo',
            $response->data->first()->title
        );

        $this->assertEquals(
            true,
            $response->data->first()->completed
        );
    }

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

    public function testCanGetBasicDataWhenDataKeyDoesntExist()
    {
        $response = $this->getMyClient()->get('/todos/1');

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

    public function getMyClient()
    {
        return new MyRestClient('https://jsonplaceholder.typicode.com');
    }
}

class MyRestClient extends RestClient
{
    protected $responseDataKey = 'not-exist';
}

class TestModel
{

}
