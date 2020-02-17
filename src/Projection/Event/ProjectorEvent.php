<?php

declare(strict_types=1);

namespace Messenger\Projection\Event;

use Messenger\Event\EventInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ProjectorEvent extends Event
{
    public const NAME = 'messenger.projector';

    /** @var EventInterface */
    private $event;

    public function __construct(EventInterface $event)
    {
        $this->event = $event;
    }

    public function getEvent(): EventInterface
    {
        return $this->event;
    }
}
