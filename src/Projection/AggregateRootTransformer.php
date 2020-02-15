<?php

declare(strict_types=1);

namespace Messenger\Projection;

use Messenger\Aggregate\AggregateRoot;
use ReflectionClass;

class AggregateRootTransformer
{
    public function transform(array $eventStream, string $aggregateRootFQCN): AggregateRoot
    {
        $class = new ReflectionClass($aggregateRootFQCN);

        /** @var AggregateRoot $aggregateRoot */
        $aggregateRoot = $class->newInstanceWithoutConstructor();
        $aggregateRoot->reconstruct($eventStream);

        return $aggregateRoot;
    }
}
