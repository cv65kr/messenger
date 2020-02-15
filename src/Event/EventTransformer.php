<?php

declare(strict_types=1);

namespace Messenger\Event;

use DateTimeImmutable;
use Messenger\Aggregate\AggregateRootId;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class EventTransformer
{
    /** @var UuidInterface */
    private $id;

    /** @var AggregateRootId */
    private $aggregateId;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var array */
    private $payload;

    /** @var int */
    private $version;

    /** @var string */
    private $event;

    private function __construct(AggregateRootId $aggregateId, array $payload, string $event)
    {
        $this->aggregateId = $aggregateId;
        $this->payload = $payload;
        $this->event = $event;
    }

    public static function create(AggregateRootId $aggregateId, array $payload, string $event): self
    {
        $event = new self($aggregateId, $payload, $event);
        $event->id = Uuid::uuid4();
        $event->version = 0;
        $event->createdAt = new DateTimeImmutable();

        return $event;
    }

    public function incrementVersion(): void
    {
        ++$this->version;
    }

    public function getAggregateId(): AggregateRootId
    {
        return $this->aggregateId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getEvent(): string
    {
        return $this->event;
    }
}
