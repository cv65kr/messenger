<?php

declare(strict_types=1);

namespace Messenger\Aggregate;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AggregateRootIdTest extends TestCase
{
    public function testConvertFromUUID(): void
    {
        $uuid = Uuid::uuid4();
        $uuidString = $uuid->toString();

        $result = AggregateRootId::fromUUID($uuid);

        self::assertSame($uuidString, (string) $result);
    }

    public function testConvertToUUID(): void
    {
        self::assertInstanceOf(UuidInterface::class, AggregateRootId::generate()->toUUID());
    }

    public function testToString(): void
    {
        self::assertIsString((string) AggregateRootId::generate());
    }
}
