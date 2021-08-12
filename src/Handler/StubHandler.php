<?php

namespace Alekseytupichenkov\GuzzleStub\Handler;

use Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException;
use Alekseytupichenkov\GuzzleStub\Model\Fixture;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class StubHandler
{
    /** @var Fixture[] */
    private array $fixtures = [];

    /**
     * @param mixed[] $options
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $response = Create::promiseFor($this->getSuitableResponse($request));

        return $response->then(
            function ($value) {
                return $value;
            }
        );
    }

    public function append(Fixture $fixture): void
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
        return $this->isSuitableString(urldecode($expectedRequest->getUri()->__toString()), urldecode($actualRequest->getUri()->__toString()));
    }

    private function isSuitableBody(RequestInterface $expectedRequest, RequestInterface $actualRequest): bool
    {
        return $this->isSuitableString($expectedRequest->getBody()->__toString(), $actualRequest->getBody()->__toString());
    }

    private function isSuitableHeaders(RequestInterface $expectedRequest, RequestInterface $actualRequest): bool
    {
        return empty($this->array_diff_recursive($expectedRequest->getHeaders(), $actualRequest->getHeaders()));
    }

    /**
     * @param mixed[] $a
     * @param mixed[] $b
     *
     * @return string[][]
     */
    private function array_diff_recursive(array $a, array $b): array
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
        return 0 === strcasecmp($expected, $actual) || @preg_match('/^'.$expected.'$/i', $actual);
    }
}
