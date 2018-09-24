<?php

namespace Alekseytupichenkov\GuzzleStubBundle\Tests\Functional;

use Alekseytupichenkov\GuzzleStubBundle\Tests\Functional\Stub\TestClientStub;
use PHPUnit\Framework\TestCase;

class StubHandlerTest extends TestCase
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
                'token' => '123'
            ],
            'body' => '{"data":"test"}'
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
                'token' => '123'
            ],
            'body' => '{"data":"test"}'
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"result":"error"}', $response->getBody()->__toString());
        $this->assertEquals(1, $this->client->getHistoryCount());
        $this->assertEquals($response, $this->client->getLatestHistoryResponse());
    }

    /**
     * @expectedException \Alekseytupichenkov\GuzzleStubBundle\Exception\GuzzleStubException
     * @expectedExceptionMessageRegExp "Can't find suitable response for request .*"
     */
    public function testException()
    {
        $this->client->post('/without/fixture');
    }
}
