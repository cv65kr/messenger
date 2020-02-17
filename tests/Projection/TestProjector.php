<?php

declare(strict_types=1);

namespace Messenger\Projection;

use Messenger\Event\TestEvent;
use Messenger\Functional\ApplicationTestCase;

class TestProjector extends Projector
{
    protected function applyTestEvent(TestEvent $testEvent): void
    {
        ApplicationTestCase::createTemporaryFile(
            'projector.test',
            $testEvent->getItem()
        );
    }
}
