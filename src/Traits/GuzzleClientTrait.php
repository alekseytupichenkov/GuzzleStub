<?php

declare(strict_types=1);

namespace Alekseytupichenkov\GuzzleStub\Traits;

use Alekseytupichenkov\GuzzleStub\Exception\GuzzleStubException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Alekseytupichenkov\GuzzleStub\Model\Fixture;
use Alekseytupichenkov\GuzzleStub\Handler\StubHandler;

trait GuzzleClientTrait
{
    /** @var HandlerStack $handlerStack */
    private $handlerStack;
    private $stubHandler;
    private $history = [];

    public function __construct()
    {
        call_user_func_array([$this, 'parent::__construct'], func_get_args());

        $this->handlerStack = $this->getConfig('handler');
        $this->handlerStack->push(Middleware::history($this->history));

        $this->stubHandler = new StubHandler();
        $this->handlerStack->setHandler($this->stubHandler);

        $this->loadFixtures();
    }

    /**
     * Append fixture
     *
     * @param Fixture $fixture
     *
     * @return self
     */
    public function append(Fixture $fixture): self
    {
        $this->stubHandler->append($fixture);

        return $this;
    }


    abstract public function getConfig($option = null);

    abstract public function loadFixtures();

    public function getHistoryCount(): int
    {
        return count($this->history);
    }

    public function getHistoryList(): array
    {
        return $this->history;
    }

    public function hasHistory(int $index): bool
    {
        return isset($this->history[$index]);
    }

    /**
     * @throws GuzzleStubException
     */
    public function getHistory(int $index): array
    {
        if (!$this->hasHistory($index)) {
            throw GuzzleStubException::cantFoundHistoryWithIndex($index);
        }

        return $this->history[$index];
    }

    /**
     * @throws \Exception
     */
    public function getLatestHistory(): array
    {
        return $this->getHistory($this->getHistoryCount() - 1);
    }

    /**
     * @throws \Exception
     */
    public function getLatestHistoryRequest(): RequestInterface
    {
        return $this->getLatestHistory()['request'];
    }

    /**
     * @throws \Exception
     */
    public function getLatestHistoryResponse(): ResponseInterface
    {
        return $this->getLatestHistory()['response'];
    }
}
