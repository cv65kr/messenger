<?php

declare(strict_types=1);

namespace Messenger\Projection;

use Messenger\Event\EventInterface;

abstract class Projector implements ProjectorInterface
{
    public function apply(EventInterface $event): void
    {
        $eventClass = \get_class($event);
        $parts = \explode('\\', $eventClass);
        $method = \sprintf('apply%s', \end($parts));

        if (\method_exists($this, $method)) {
            $this->$method($event);
        }
    }
}
