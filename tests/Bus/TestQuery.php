<?php

declare(strict_types=1);

namespace Messenger\Bus;

class TestQuery implements QueryInterface
{
    /** @var string */
    public $item;

    public function __construct(string $item)
    {
        $this->item = $item;
    }

    public function getItem(): string
    {
        return $this->item;
    }
}
