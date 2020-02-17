<?php

declare(strict_types=1);

namespace Messenger\Bus;

use Messenger\Bus\Handler\CommandHandlerInterface;
use Messenger\Functional\ApplicationTestCase;

class TestCommandHandler implements CommandHandlerInterface
{
    public function __invoke(TestCommand $command)
    {
        ApplicationTestCase::createTemporaryFile('command.test', $command->item);
    }
}
