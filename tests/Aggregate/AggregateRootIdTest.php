<?php

declare(strict_types=1);

namespace Messenger\Aggregate;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AggregateRootIdTest extends TestCase
{
    public function testConvertFromUUID(): void
    {
        $uuid = Uuid::uuid4();
        $uuidString = $uuid->toString();

        $result = AggregateRootId::fromUUID($uuid);

        self::assertSame($uuidString, (string) $result);
    }
}