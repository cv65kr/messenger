<?php

declare(strict_types=1);

namespace Messenger\Bus;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBusTest extends TestCase
{
    /** @var CommandBus */
    private $bus;

    /** @var TestCommand */
    private $command;

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new TestCommand();
        $messageBus = $this->prophesize(MessageBusInterface::class);
        $messageBus->dispatch($this->command)->shouldBeCalledOnce()->willReturn(new Envelope(new \stdClass()));

        $this->bus = new CommandBus($messageBus->reveal());
    }

    public function testHandle(): void
    {
        $this->bus->handle($this->command);

        self::assertTrue(true);
    }
}

class TestCommand implements CommandInterface
{
}
