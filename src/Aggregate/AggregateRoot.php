<?php

declare(strict_types=1);

namespace Messenger\Aggregate;

use Messenger\Entity\EventStream;
use Messenger\Event\EventInterface;
use Messenger\Event\EventTransformer;

abstract class AggregateRoot
{
    /** @var EventInterface[] */
    protected $recordedEvents = [];

    /** @var int */
    protected $version = 0;

    abstract public function getAggregateRootId(): AggregateRootId;

    /**
     * @param EventStream[] $eventStream
     */
    public function reconstruct(array $eventStream)
    {
        foreach ($eventStream as $event) {
            $eventFQCN = $event->getEvent();

            ++$this->version;
            $this->apply(
                $eventFQCN::deserialize($event->getPayload()),
                false
            );
        }
    }

    public function getUnrecordedEvents(): array
    {
        $stream = $this->recordedEvents;

        $this->recordedEvents = [];

        return $stream;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    protected function apply(EventInterface $event, bool $recordEvent = true): void
    {
        $eventClass = \get_class($event);
        $parts = \explode('\\', $eventClass);
        $method = \sprintf('apply%s', \end($parts));

        if (\method_exists($this, $method)) {
            $this->$method($event);
        }

        if (!$recordEvent) {
            return;
        }

        $transformedEvent = EventTransformer::create(
            $this->getAggregateRootId(),
            $event->serialize(),
            \get_class($event)
        );

        $transformedEvent->incrementVersion();

        $this->version += $transformedEvent->getVersion();

        $this->recordedEvents[] = $transformedEvent;
    }
}
