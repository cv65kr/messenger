<?php

declare(strict_types=1);

namespace Messenger\Bus;

class TestCommand implements CommandInterface
{
    /** @var string */
    public $item;

    public function __construct(string $item)
    {
        $this->item = $item;
    }
}
