<?php

declare(strict_types=1);

namespace Messenger\Projection;

use Messenger\Event\EventInterface;

interface ProjectorInterface
{
    public function apply(EventInterface $event): void;
}
