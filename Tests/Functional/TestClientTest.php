<?php

namespace Alekseytupichenkov\GuzzleStub\Tests\Functional;

use Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException;
use Alekseytupichenkov\GuzzleStub\Tests\Functional\Stub\TestClientStub;
use PHPUnit\Framework\TestCase;

class TestClientTest extends TestCase
{
    /** @var TestClientStub */
    private $client;

    protected function setUp(): void
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

    public function testCantFindSuitableResponseException()
    {
        $this->expectException(GuzzleStubException::class);
        $this->expectErrorMessageMatches('/^Can`t find suitable response for request .*/');

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

    public function testCantFoundHistoryWithIndexException()
    {
        $this->expectException(GuzzleStubException::class);
        $this->expectExceptionMessage('Can`t found history with index 1');

        $this->client->getHistory(1);
    }
}
