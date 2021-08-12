<?php

namespace Alekseytupichenkov\GuzzleStub\Model;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Fixture
{
    public function __construct(private RequestInterface $request, private ResponseInterface $response)
    {
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
