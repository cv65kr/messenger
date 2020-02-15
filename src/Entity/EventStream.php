<?php

declare(strict_types=1);

namespace Messenger\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="Messenger\Repository\EventStreamRepository")
 * @ORM\Table(
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="aggregate_id_version_unique", columns={"aggregate_id", "version"})
 *     }
 * )
 */
class EventStream
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $event;

    /**
     * @var UuidInterface
     *
     * @ORM\Column(type="uuid_binary")
     */
    private $aggregateId;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $payload;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    public function __construct(
        UuidInterface $id,
        string $event,
        UuidInterface $aggregateId,
        array $payload,
        DateTimeImmutable $createdAt,
        int $version
    ) {
        $this->id = $id;
        $this->event = $event;
        $this->aggregateId = $aggregateId;
        $this->payload = $payload;
        $this->createdAt = $createdAt;
        $this->version = $version;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getAggregateId(): UuidInterface
    {
        return $this->aggregateId;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getVersion(): int
    {
        return $this->version;
    }
}
