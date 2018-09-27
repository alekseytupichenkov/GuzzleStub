<?php

namespace Alekseytupichenkov\GuzzleStub\Tests\Functional;

use Alekseytupichenkov\GuzzleStub\Tests\Functional\Stub\TestClientStub;
use PHPUnit\Framework\TestCase;

class TestClientTest extends TestCase
{
    /** @var TestClientStub */
    private $client;

    public function setUp()
    {
        $this->client = new TestClientStub();
    }

    public function testSuccess()
    {
        $response = $this->client->post('/foo/bar', [
            'headers' => [
                'token' => '123',
            ],
            'body' => '{"data":"test"}',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"result":"ok"}', $response->getBody()->__toString());
        $this->assertEquals(1, $this->client->getHistoryCount());
        $this->assertEquals($response, $this->client->getLatestHistoryResponse());
    }

    public function testUnsuccess()
    {
        $response = $this->client->post('/foo/baz', [
            'headers' => [
                'token' => '123',
            ],
            'body' => '{"data":"test"}',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"result":"error"}', $response->getBody()->__toString());
        $this->assertEquals(1, $this->client->getHistoryCount());
        $this->assertEquals($response, $this->client->getLatestHistoryResponse());
    }

    /**
     * @expectedException \Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException
     * @expectedExceptionMessageRegExp "Can`t find suitable response for request .*"
     */
    public function testCantFindSuitableResponseException()
    {
        $this->client->post('/without/fixture');
    }

    public function testHistory()
    {
        $this->client->post('/foo/bar', [
            'headers' => [
                'token' => '123',
            ],
            'body' => '{"data":"test"}',
        ]);

        $history = $this->client->getHistoryList();
        $this->assertCount(1, $history);
        $this->assertEquals($this->client->getLatestHistoryRequest(), $history[0]['request']);
        $this->assertEquals($this->client->getLatestHistoryResponse(), $history[0]['response']);
        $this->assertEquals(null, $history[0]['error']);
        $this->assertArrayHasKey('options', $history[0]);
    }

    public function testHistoryByIndex()
    {
        $this->client->post('/foo/bar', [
            'headers' => [
                'token' => '123',
            ],
            'body' => '{"data":"test"}',
        ]);

        $history = $this->client->getHistory(0);
        $this->assertEquals($this->client->getLatestHistoryRequest(), $history['request']);
        $this->assertEquals($this->client->getLatestHistoryResponse(), $history['response']);
        $this->assertEquals(null, $history['error']);
        $this->assertArrayHasKey('options', $history);
    }

    /**
     * @expectedException \Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException
     * @expectedExceptionMessage Can`t found history with index 1
     */
    public function testCantFoundHistoryWithIndexException()
    {
        $this->client->getHistory(1);
    }
}
