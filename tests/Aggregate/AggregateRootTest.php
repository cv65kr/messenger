<?php

declare(strict_types=1);

namespace Messenger\Aggregate;

use Messenger\Entity\EventStream;
use Messenger\Event\EventInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AggregateRootTest extends TestCase
{
    public function testGetUnrecordedEvents(): void
    {
        $aggregateRoot = UserAggregateRoot::create(Uuid::uuid4());
        $unrecordedEvents = $aggregateRoot->getUnrecordedEvents();

        self::assertCount(1, $unrecordedEvents);
        self::assertSame(1, $aggregateRoot->getVersion());
    }

    public function testReconstruct(): void
    {
        $eventStream = new EventStream(
            Uuid::uuid4(),
            UserWasCreated::class,
            Uuid::uuid4(),
            [
                'uuid' => '057ae130-4302-4f4e-9cd5-5334b157565b',
            ],
            new \DateTimeImmutable(),
            1
        );

        $aggregateRoot = new UserAggregateRoot();
        $aggregateRoot->reconstruct([$eventStream]);

        $unrecordedEvents = $aggregateRoot->getUnrecordedEvents();

        self::assertCount(0, $unrecordedEvents);
        self::assertSame(1, $aggregateRoot->getVersion());
    }
}

class UserAggregateRoot extends AggregateRoot
{
    /** @var UuidInterface */
    private $uuid;

    public static function create(UuidInterface $uuid)
    {
        $user = new self();
        $user->apply(new UserWasCreated($uuid));

        return $user;
    }

    protected function applyUserWasCreated(UserWasCreated $event): void
    {
        $this->uuid = $event->uuid;
    }

    public function uuid(): string
    {
        return $this->uuid->toString();
    }

    public function getAggregateRootId(): AggregateRootId
    {
        return AggregateRootId::fromUUID($this->uuid);
    }
}

class UserWasCreated implements EventInterface
{
    /** @var UuidInterface */
    public $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function deserialize(array $data)
    {
        return new self(Uuid::fromString($data['uuid']));
    }

    public function serialize(): array
    {
        return [
            'uuid' => $this->uuid->toString(),
        ];
    }
}
