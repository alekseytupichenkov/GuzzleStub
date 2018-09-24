<?php

namespace Alekseytupichenkov\GuzzleStubBundle\Tests\Unit\Model;

use Alekseytupichenkov\GuzzleStubBundle\Model\Fixture;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

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
