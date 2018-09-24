# GuzzleStubBundle

Table of contents:

- [Installation](#installation)
- [Basic usage and methods](#basic-usage)

Introduction
============

This bundles provides guzzle stubs.

Installation
------------

 1. Download the Bundle

    Run command in console

    ```bash
    $ composer require --dev alekseytupichenkov/guzzle_stub_bundle
    ```

Basic usage
-----------

In case of you have guzzle client for which it is necessary to write stubs and exclude the possibility make requests to external services

For example:
```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class MySuperGuzzleClient extends Client
{
    public function __construct(HandlerStack $handlerStack = null)
    {
        parent::__construct([
            'timeout' => 300,
            'base_uri' => 'http://foo.bar',
            'handler' => $handlerStack ?? HandlerStack::create(),
        ]);
    }
}
```


Create Guzzle client stub class using existing guzzle client, add trait GuzzleClientTrait, implement method `loadFixtures` and add fixtures to it
```php
use Alekseytupichenkov\GuzzleStubBundle\Model\Fixture;
use Alekseytupichenkov\GuzzleStubBundle\Traits\GuzzleClientTrait;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MySuperGuzzleClientStub extends MySuperGuzzleClient
{
    use GuzzleClientTrait;

    function loadFixtures()
    {
        $this->append(new Fixture(
            new Request('GET', 'http://foo.bar/baz', ['token' => '.*']),
            new Response(200, [], '{"result":"ok"}')
        ));

        $this->append(new Fixture(
            new Request('POST', 'http://foo.bar/baz', ['token' => '.*'], '{"data":".*"}'),
            new Response(200, [], '{"result":"ok"}')
        ));
    }
}
```

Now you can write tests checking history of requests/responses
```php
use PHPUnit\Framework\TestCase;

class MySuperGuzzleClientStubTest extends TestCase
{
    /** @var MySuperGuzzleClientStub */
    private $client;

    public function setUp()
    {
        $this->client = new MySuperGuzzleClientStub();
    }

    public function testPost()
    {
        $response = $this->client->post('/baz', [
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
}
```

If stub haven't sutible fixture, you'll get exception like this
```php
1) MySuperGuzzleClientStubTest::testException
Alekseytupichenkov\GuzzleStubBundle\Exception\GuzzleStubException: Can't find suitable response for request [array (
  'method' => 'POST',
  'uri' => '/without/fixture',
  'body' => '',
  'protocol_version' => '1.1',
  'headers' =>
  array (
    'User-Agent' =>
    array (
      0 => 'GuzzleHttp/6.3.3 curl/7.54.0 PHP/7.1.18',
    ),
    'Host' =>
    array (
      0 => 'foo.bar',
    ),
  ),
)]
```
