<?php

declare(strict_types=1);

namespace Alekseytupichenkov\GuzzleStubBundle\Tests\Fixtures\Stub;

use Alekseytupichenkov\GuzzleStubBundle\Model\Fixture;
use Alekseytupichenkov\GuzzleStubBundle\Tests\Fixtures\Client\TestClient;
use Alekseytupichenkov\GuzzleStubBundle\Traits\GuzzleClientTrait;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class TestClientStub extends TestClient
{
    use GuzzleClientTrait;

    function loadFixtures()
    {
        $this->append(new Fixture(
            new Request('POST', 'http://foo.bar/foo/bar', [
                'token' => '.*'
            ], '{"data":".*"}'),
            new Response(200, [], '{"result":"ok"}')
        ));

        $this->append(new Fixture(
            new Request('POST', 'http://foo.bar/foo/baz', [
                'token' => '.*'
            ], '{"data":".*"}'),
            new Response(200, [], '{"result":"error"}')
        ));
    }
}
