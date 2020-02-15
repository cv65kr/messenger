<?php

declare(strict_types=1);

namespace Messenger\Repository;

use Assert\Assertion as Assert;
use Messenger\Aggregate\AggregateRoot;
use Messenger\Aggregate\AggregateRootId;
use Messenger\Bus\EventBus;
use Messenger\Entity\EventStream;
use Messenger\Event\EventInterface;
use Messenger\Event\EventTransformer;
use Messenger\Projection\AggregateRootTransformer;
use Messenger\Projection\Event\ProjectorEvent;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class EventSourcingRepository
{
    /** @var EventStreamRepository */
    private $eventStreamRepository;

    /** @var EventBus */
    private $eventBus;

    /** @var AggregateRootTransformer */
    private $aggregateRootTransformer;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        EventStreamRepository $eventStreamRepository,
        EventBus $eventBus,
        AggregateRootTransformer $aggregateRootTransformer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventStreamRepository = $eventStreamRepository;
        $this->eventBus = $eventBus;
        $this->aggregateRootTransformer = $aggregateRootTransformer;
        $this->eventDispatcher = $eventDispatcher;
    }

    abstract public function getAggregateRoot(): string;

    public function load(AggregateRootId $aggregateRootId): AggregateRoot
    {
        $eventStream = $this->eventStreamRepository->findByAggregateRootId($aggregateRootId);

        return $this->aggregateRootTransformer->transform($eventStream, $this->getAggregateRoot());
    }

    public function save(AggregateRoot $aggregateRoot): void
    {
        Assert::isInstanceOf($aggregateRoot, $this->getAggregateRoot());

        /** @var EventTransformer $event */
        foreach ($aggregateRoot->getUnrecordedEvents() as $event) {
            $currentEventFQCN = $event->getEvent();

            $this->eventStreamRepository->save(
                new EventStream(
                    Uuid::uuid4(),
                    $currentEventFQCN,
                    $aggregateRoot->getAggregateRootId()->toUUID(),
                    $event->getPayload(),
                    $event->getCreatedAt(),
                    $aggregateRoot->getVersion()
                )
            );

            /** @var EventInterface $currentEvent */
            $currentEvent = $currentEventFQCN::deserialize($event->getPayload());

            $this->eventDispatcher->dispatch(new ProjectorEvent($currentEvent), ProjectorEvent::NAME);
            $this->eventBus->handle($currentEvent);
        }
    }
}
