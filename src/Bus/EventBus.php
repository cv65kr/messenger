<?php

declare(strict_types=1);

namespace Messenger\Bus;

use Messenger\Event\EventInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventBus
{
    use MessageBusExceptionTrait;

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @throws \Throwable
     */
    public function handle(EventInterface $event): void
    {
        try {
            $this->messageBus->dispatch(new Envelope($event));
        } catch (HandlerFailedException $e) {
            $this->throwException($e);
        }
    }
}
