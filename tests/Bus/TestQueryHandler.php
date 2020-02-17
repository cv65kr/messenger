<?php

declare(strict_types=1);

namespace Messenger\Bus;

use Messenger\Bus\Handler\QueryHandlerInterface;

class TestQueryHandler implements QueryHandlerInterface
{
    public function __invoke(TestQuery $query): string
    {
        return 'TEST ' . $query->item;
    }
}
