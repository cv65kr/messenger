<?php

declare(strict_types=1);

namespace Messenger\Bus;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBusTest extends TestCase
{
    /** @var TestQuery */
    private $query;

    /** @var QueryBus */
    private $bus;

    public function setUp(): void
    {
        parent::setUp();

        $this->query = new TestQuery();

        $messageBus = $this->prophesize(MessageBusInterface::class);
        $messageBus
            ->dispatch($this->query)
            ->shouldBeCalledOnce()
            ->willReturn(new Envelope(
                new \stdClass(),
                [
                    new HandledStamp(['test' => 'test1'], 'testName')
                ]
            ));

        $this->bus = new QueryBus($messageBus->reveal());
    }

    public function testHandle(): void
    {
        $result = $this->bus->handle($this->query);

        self::assertSame('test1', $result['test']);
    }
}

class TestQuery implements QueryInterface
{
}
