<?php

namespace Alekseytupichenkov\GuzzleStub\Tests\Unit\Handler;

use Alekseytupichenkov\GuzzleStub\Handler\StubHandler;
use Alekseytupichenkov\GuzzleStub\Model\Fixture;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class StubHandlerTest extends TestCase
{
    /** @var Request */
    private $getRequest;

    /** @var Response */
    private $getResponse;

    /** @var Request */
    private $postRequest;

    /** @var Response */
    private $postResponse;

    /** @var StubHandler */
    private $handler;

    public function setUp()
    {
        $this->handler = new StubHandler();

        $this->getRequest = new Request('GET', '/foo/bar?test=1', ['foo' => 'bar']);
        $this->getResponse = new Response(200, ['baz' => 'POST'], '{"get": "ok"}');
        $this->postRequest = new Request('POST', '/foo/bar', ['foo' => 'bar'], 'SuperBody');
        $this->postResponse = new Response(200, ['baz' => 'POST'], '{"post": "ok"}');

        $this->handler->append(new Fixture($this->getRequest, $this->getResponse));
        $this->handler->append(new Fixture($this->postRequest, $this->postResponse));
    }

    public function testSuccessSimpleGet()
    {
        $promise = $this->handler->__invoke($this->getRequest, []);

        $this->assertEquals($this->getResponse, $promise->wait());
    }

    public function testSuccessSimplePost()
    {
        $promise = $this->handler->__invoke($this->postRequest, []);

        $this->assertEquals($this->postResponse, $promise->wait());
    }

    public function testSuccessParticleGet()
    {
        $request = new Request('GET', '/foo/bar?test=1');
        $response = new Response(200);
        $this->handler->append(new Fixture($request, $response));

        $promise = $this->handler->__invoke($this->getRequest, []);

        $this->assertEquals($response, $promise->wait());
    }

    public function testSuccessParticlePost()
    {
        $request = new Request('POST', '/foo/bar');
        $response = new Response(200);
        $this->handler->append(new Fixture($request, $response));

        $promise = $this->handler->__invoke($this->postRequest, []);

        $this->assertEquals($this->postResponse, $promise->wait());
    }

    public function testSuccessRegexGet()
    {
        $request = new Request('GET|POST', '\/foo\/.*', ['foo' => '.*']);
        $response = new Response(200);
        $this->handler->append(new Fixture($request, $response));

        $promise = $this->handler->__invoke($this->getRequest, []);

        $this->assertEquals($response, $promise->wait());
    }

    public function testSuccessRegexPost()
    {
        $request = new Request('GET|POST', '\/foo\/.*', ['foo' => '.*'], 'Super.*');
        $response = new Response(200);
        $this->handler->append(new Fixture($request, $response));

        $promise = $this->handler->__invoke($this->postRequest, []);

        $this->assertEquals($response, $promise->wait());
    }

    public function testReplaceResponseByExistingRequest()
    {
        $response = new Response(200);
        $this->handler->append(new Fixture($this->getRequest, $response));

        $promise = $this->handler->__invoke($this->getRequest, []);

        $this->assertEquals($response, $promise->wait());
    }

    /**
     * @expectedException \Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException
     * @expectedExceptionMessageRegExp "Can`t find suitable response for request .*"
     */
    public function testException()
    {
        $this->handler->__invoke(new Request('not', 'valid'), []);
    }
}
