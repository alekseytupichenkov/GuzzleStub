<?php

declare(strict_types=1);

namespace Alekseytupichenkov\GuzzleStubBundle\Tests\Functional\Client;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class TestClient extends Client
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
