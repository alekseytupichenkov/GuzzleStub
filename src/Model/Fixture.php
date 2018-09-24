<?php

namespace Alekseytupichenkov\GuzzleStub\Model;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Fixture
{
    private $request;
    private $response;

    public function __construct(RequestInterface $request, ResponseInterface $response) {
        $this->request = $request;
        $this->response = $response;
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
