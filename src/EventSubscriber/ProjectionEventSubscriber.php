<?php

declare(strict_types=1);

namespace Messenger\EventSubscriber;

use Messenger\Projection\Event\ProjectorEvent;
use Messenger\Projection\ProjectorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProjectionEventSubscriber implements EventSubscriberInterface
{
    /** @var ProjectorInterface[] */
    private $projectors;

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProjectorEvent::NAME => 'handleProjection',
        ];
    }

    public function addProjector(ProjectorInterface $projector): void
    {
        $this->projectors[] = $projector;
    }

    public function handleProjection(ProjectorEvent $projectorEvent): void
    {
        $event = $projectorEvent->getEvent();

        foreach ($this->projectors as $projector) {
            /** @var ProjectorInterface $service */
            $service = $this->container->get(\get_class($projector));
            $service->apply($event);
        }
    }
}
