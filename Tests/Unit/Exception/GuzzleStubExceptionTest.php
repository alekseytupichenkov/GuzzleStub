<?php

declare(strict_types=1);

namespace Alekseytupichenkov\GuzzleStub\Tests\Unit\Exception;

use Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class GuzzleStubExceptionTest extends TestCase
{
    public function testResponseNotFound()
    {
        $request = new Request('POST', 'http://foo.bar/baz', [
            'foo' => 'bar',
            'baz' => 1,
        ], 'SuperBody');

        $e = GuzzleStubException::responseNotFound($request);

        $this->assertEquals("Can`t find suitable response for request [array (
  'method' => 'POST',
  'uri' => 'http://foo.bar/baz',
  'body' => 'SuperBody',
  'protocol_version' => '1.1',
  'headers' => 
  array (
    'Host' => 
    array (
      0 => 'foo.bar',
    ),
    'foo' => 
    array (
      0 => 'bar',
    ),
    'baz' => 
    array (
      0 => '1',
    ),
  ),
)]", $e->getMessage());
    }

    public function testCantFoundHistoryWithIndex()
    {
        $e = GuzzleStubException::cantFoundHistoryWithIndex(1);

        $this->assertEquals('Can`t found history with index 1', $e->getMessage());
    }
}
