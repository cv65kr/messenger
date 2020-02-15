<?php

declare(strict_types=1);

namespace Messenger\Projection;

use Messenger\Event\EventInterface;

interface ReadModelInterface
{
    public static function fromSerializable(EventInterface $event);

    public function serialize(): array;

    public function getId(): string;
}
