<?php

namespace Alekseytupichenkov\GuzzleStubBundle\Handler;

use Alekseytupichenkov\GuzzleStubBundle\Model\Fixture;
use Alekseytupichenkov\GuzzleStubBundle\Exception\GuzzleStubException;
use GuzzleHttp\Promise\RejectedPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class StubHandler
{
    private $fixtures = [];

    public function __invoke(RequestInterface $request, array $options)
    {
        $response = \GuzzleHttp\Promise\promise_for($this->getSuitableResponse($request));

        return $response->then(
            function ($value) use ($request, $options) {
                return $value;
            },
            function ($reason) use ($request, $options) {
                return new RejectedPromise($reason);
            }
        );
    }

    public function append(Fixture $fixture)
    {
        $this->fixtures[] = $fixture;
    }

    private function getSuitableResponse(RequestInterface $request): ResponseInterface
    {
        foreach (array_reverse($this->fixtures) as $fixture) {
            /** @var Fixture $fixture */
            if (
                !$this->isSuitableMethod($fixture->getRequest(), $request) ||
                !$this->isSuitableUri($fixture->getRequest(), $request) ||
                !$this->isSuitableBody($fixture->getRequest(), $request) ||
                !$this->isSuitableHeaders($fixture->getRequest(), $request)
            ) {
                continue;
            }

            return $fixture->getResponse();
        }

        throw GuzzleStubException::responseNotFound($request);
    }

    private function isSuitableMethod(RequestInterface $expectedRequest, RequestInterface $actualRequest): bool
    {
        return $this->isSuitableString($expectedRequest->getMethod(), $actualRequest->getMethod());
    }

    private function isSuitableUri(RequestInterface $expectedRequest, RequestInterface $actualRequest): bool
    {
        return $this->isSuitableString($expectedRequest->getUri()->getPath(), $actualRequest->getUri()->getPath());
    }

    private function isSuitableBody(RequestInterface $expectedRequest, RequestInterface $actualRequest): bool
    {
        return $this->isSuitableString($expectedRequest->getBody()->__toString(), $actualRequest->getBody()->__toString());
    }

    private function isSuitableHeaders(RequestInterface $expectedRequest, RequestInterface $actualRequest): bool
    {
        return empty($this->array_diff_recursive($expectedRequest->getHeaders(), $actualRequest->getHeaders()));
    }

    private function array_diff_recursive($a, $b)
    {
        $result = [];

        foreach ($a as $key => $value) {
            if (array_key_exists($key, $b)) {
                if (is_array($value)) {
                    $diff = $this->array_diff_recursive($value, $b[$key]);
                    if (count($diff)) {
                        $result[$key] = $diff;
                    }
                } else {
                    if (!$this->isSuitableString($value, $b[$key])) {
                        $result[$key] = $value;
                    }
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function isSuitableString(string $expected, string $actual): bool
    {
        return 0 === strcasecmp($expected, $actual) || @preg_match('/^' . $expected . '$/i', $actual);
    }
}
