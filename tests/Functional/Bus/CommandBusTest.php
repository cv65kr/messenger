<?php

declare(strict_types=1);

namespace Messenger\Functional\Bus;

use Messenger\Bus\CommandInterface;
use Messenger\Bus\TestCommand;
use Messenger\Functional\ApplicationTestCase;
use Throwable;

class CommandBusTest extends ApplicationTestCase
{
    public function testCommandBus(): void
    {
        $this->handle(new TestCommand('TEST FILE'));

        self::assertSame('TEST FILE', $this->getTemporaryFile('command.test'));
    }

    public function testThrowsExceptionWhenHandlerIsWrong(): void
    {
        self::expectException(Throwable::class);

        $this->handle(new class() implements CommandInterface {
        });
    }
}
