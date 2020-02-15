<?php

declare(strict_types=1);

namespace Messenger\Event;

interface EventInterface
{
    public static function deserialize(array $data);

    public function serialize(): array;
}
