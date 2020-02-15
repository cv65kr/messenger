<?php

declare(strict_types=1);

namespace Messenger\Bus;

use Messenger\Event\EventInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBusTest extends TestCase
{
    /** @var EventBus */
    private $bus;

    /** @var TestEvent */
    private $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->event = new TestEvent();
        $messageBus = $this->prophesize(MessageBusInterface::class);
        $messageBus
            ->dispatch(new Envelope($this->event))
            ->shouldBeCalledOnce()
            ->willReturn(new Envelope(new \stdClass()))
        ;

        $this->bus = new EventBus($messageBus->reveal());
    }

    public function testHandle(): void
    {
        $this->bus->handle($this->event);

        self::assertTrue(true);
    }
}

class TestEvent implements EventInterface
{
    public static function deserialize(array $data)
    {
        return new self();
    }

    public function serialize(): array
    {
        return [];
    }
}
