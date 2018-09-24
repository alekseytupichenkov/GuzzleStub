<?php

declare(strict_types=1);

namespace Alekseytupichenkov\GuzzleStub\Tests\Functional\Stub;

use Alekseytupichenkov\GuzzleStub\Model\Fixture;
use Alekseytupichenkov\GuzzleStub\Tests\Functional\Client\TestClient;
use Alekseytupichenkov\GuzzleStub\Traits\GuzzleClientTrait;
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
