<?php

declare(strict_types=1);

namespace Messenger\Functional\EventSubscriber;

use Messenger\Event\EventInterface;
use Messenger\Event\TestEvent;
use Messenger\EventSubscriber\ProjectionEventSubscriber;
use Messenger\Functional\ApplicationTestCase;
use Messenger\Projection\Event\ProjectorEvent;
use Messenger\Projection\Projector;
use Messenger\Projection\TestProjector;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ProjectionEventSubscriberTest extends ApplicationTestCase
{
    /** @var ProjectorEvent */
    private $event;

    public function setUp(): void
    {
        parent::setUp();

        $this->event = new ProjectorEvent(
            TestEvent::deserialize(['item' => 'test_item'])
        );
    }

    public function testHandleProjection(): void
    {
        /** @var ProjectionEventSubscriber $eventSubscriber */
        $eventSubscriber = $this->service(ProjectionEventSubscriber::class);
        $eventSubscriber->addProjector(new TestProjector());

        $eventSubscriber->handleProjection($this->event);

        self::assertSame('test_item', $this->getTemporaryFile('projector.test'));
    }

    public function testHandleProjectionWhenApplyMethodNotExists()
    {
        /** @var ProjectionEventSubscriber $eventSubscriber */
        $eventSubscriber = $this->service(ProjectionEventSubscriber::class);
        $eventSubscriber->addProjector(new TestProjector());

        $eventSubscriber->handleProjection(new ProjectorEvent(
            new class() implements EventInterface {
                public static function deserialize(array $data): self
                {
                    return new self();
                }

                public function serialize(): array
                {
                    return [];
                }
            }
        ));

        self::assertTrue(true);
    }

    public function testThrowsExceptionWhenServiceNotExists(): void
    {
        self::expectException(ServiceNotFoundException::class);

        /** @var ProjectionEventSubscriber $eventSubscriber */
        $eventSubscriber = $this->service(ProjectionEventSubscriber::class);
        $eventSubscriber->addProjector(new class() extends Projector {
        });

        $eventSubscriber->handleProjection($this->event);
    }
}
