<?php

declare(strict_types=1);

namespace Alekseytupichenkov\GuzzleStub\Exception;

use Psr\Http\Message\RequestInterface;

class GuzzleStubException extends \Exception
{
    public static function responseNotFound(RequestInterface $request): self
    {
        $data = [
            'method' => $request->getMethod(),
            'uri'    => $request->getUri()->__toString(),
            'body'   => $request->getBody()->__toString(),
        ];

        if (!empty($request->getProtocolVersion())) {
            $data['protocol_version'] = $request->getProtocolVersion();
        }

        if (!empty($request->getHeaders())) {
            $data['headers'] = $request->getHeaders();
        }

        return new self(sprintf('Can`t find suitable response for request [%s]', var_export($data, true)));
    }

    public static function cantFoundHistoryWithIndex(int $index): self
    {
        return new self(sprintf('Can`t found history with index %d', $index));
    }
}
