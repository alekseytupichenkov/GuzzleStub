<?php

namespace Alekseytupichenkov\GuzzleStub\Tests\Unit\Model;

use Alekseytupichenkov\GuzzleStub\Model\Fixture;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class FixtureTest extends TestCase
{
    public function test()
    {
        $request = new Request('GET', '');
        $response = new Response(200);

        $fixture = new Fixture($request, $response);

        $this->assertEquals($request, $fixture->getRequest());
        $this->assertEquals($response, $fixture->getResponse());
    }
}
