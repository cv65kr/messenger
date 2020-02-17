<?php

declare(strict_types=1);

namespace Messenger\Functional\Bus;

use Messenger\Bus\QueryInterface;
use Messenger\Bus\TestQuery;
use Messenger\Functional\ApplicationTestCase;
use Throwable;

class QueryBusTest extends ApplicationTestCase
{
    public function testQueryBus(): void
    {
        $result = $this->ask(new TestQuery('QUERY'));

        self::assertSame('TEST QUERY', $result);
    }

    public function testThrowsExceptionWhenHandlerIsWrong(): void
    {
        self::expectException(Throwable::class);

        $this->ask(new class() implements QueryInterface {
        });
    }
}
