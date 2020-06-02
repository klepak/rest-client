<?php

namespace Klepak\RestClient\Tests\Unit\Clients;

use Illuminate\Support\Collection;
use Klepak\RestClient\Clients\RestClient;
use Klepak\RestClient\Interfaces\TokenInterface;
use Klepak\RestClient\Tests\TestCase;

class RestClientTest extends TestCase
{
    public function testCanMergeOptions()
    {
        $client = $this->getClient();
        $client->token(new \Klepak\RestClient\Tests\Stubs\MyToken());

        $options = $client->mergeOptions([
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->assertArrayHasKey('Content-Type', $options['headers']);
        $this->assertArrayHasKey('Authorization', $options['headers']);
    }

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

    public function testCanPostAsJson()
    {
        $response = $this->getClient()->postAsJson('/todos', [
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

    public function testCanPostJsonString()
    {
        $response = $this->getClient()->postJsonString('/todos', json_encode([
            'title' => 'Test todo string',
            'completed' => true
        ]));

        $this->assertEquals(
            'Test todo string',
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
            \Klepak\RestClient\Tests\Stubs\TestModel::class,
            $response->asModel(\Klepak\RestClient\Tests\Stubs\TestModel::class)->models()->first()
        );
    }

    public function getClient()
    {
        return new RestClient('https://jsonplaceholder.typicode.com');
    }

    public function getMyClient()
    {
        return new \Klepak\RestClient\Tests\Stubs\MyRestClient('https://jsonplaceholder.typicode.com');
    }
}
